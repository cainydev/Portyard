<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class DeliverWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $timeout = 30;

    protected const MAX_RESPONSE_BYTES = 65_536;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public string $webhookId,
        public string $event,
        public array $payload,
        public ?string $deliveryId = null,
    ) {
        $this->onQueue('webhooks');
        $this->deliveryId ??= (string) Str::uuid();
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [30, 120, 600, 3600, 21600];
    }

    public function retryUntil(): Carbon
    {
        return now()->addDay();
    }

    public function handle(): void
    {
        $webhook = Webhook::find($this->webhookId);

        if (! $webhook) {
            Log::warning('DeliverWebhookJob: webhook missing, aborting', ['webhook_id' => $this->webhookId]);

            return;
        }

        $body = json_encode($this->payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $signature = $this->sign($body, $webhook->secret);

        $headers = [
            'Content-Type' => 'application/json',
            'User-Agent' => 'Portyard-Webhook/1.0',
            'X-Portyard-Event' => $this->event,
            'X-Portyard-Delivery' => $this->deliveryId,
        ];

        if ($signature !== null) {
            $headers['X-Portyard-Signature-256'] = 'sha256='.$signature;
        }

        $delivery = WebhookDelivery::updateOrCreate(
            ['id' => $this->deliveryId],
            [
                'webhook_id' => $webhook->id,
                'event' => $this->event,
                'status' => WebhookDelivery::STATUS_PENDING,
                'attempt' => $this->attempts(),
                'request_headers' => $this->redactHeaders($headers),
                'request_body' => $this->payload,
            ]
        );

        $startedAt = microtime(true);

        if (app()->isProduction() && ! $this->hostIsSafe($webhook->url)) {
            $delivery->update([
                'status' => WebhookDelivery::STATUS_FAILED,
                'error' => 'Refusing to deliver to a private or loopback address.',
                'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
            ]);

            return;
        }

        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->withoutRedirecting()
                ->withHeaders($headers)
                ->withBody($body, 'application/json')
                ->post($webhook->url);
        } catch (Throwable $e) {
            $delivery->update([
                'status' => WebhookDelivery::STATUS_FAILED,
                'error' => Str::limit($e->getMessage(), 1000),
                'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
            ]);

            throw $e;
        }

        $duration = (int) ((microtime(true) - $startedAt) * 1000);

        $delivery->update([
            'status' => $response->successful() ? WebhookDelivery::STATUS_SUCCESS : WebhookDelivery::STATUS_FAILED,
            'response_status' => $response->status(),
            'response_headers' => $this->flattenHeaders($response->headers()),
            'response_body' => Str::limit((string) $response->body(), self::MAX_RESPONSE_BYTES, ''),
            'duration_ms' => $duration,
            'delivered_at' => $response->successful() ? now() : null,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException("Webhook delivery failed with status {$response->status()}");
        }
    }

    public function failed(Throwable $exception): void
    {
        if (! $this->deliveryId) {
            return;
        }

        WebhookDelivery::where('id', $this->deliveryId)->update([
            'status' => WebhookDelivery::STATUS_FAILED,
            'error' => Str::limit($exception->getMessage(), 1000),
        ]);
    }

    /**
     * Enqueue a fresh delivery reusing the original payload (keeps the prior row for audit).
     */
    public static function redeliver(WebhookDelivery $delivery): void
    {
        self::dispatch(
            $delivery->webhook_id,
            $delivery->event,
            $delivery->request_body ?? [],
        );
    }

    protected function sign(string $body, ?string $secret): ?string
    {
        if (blank($secret)) {
            return null;
        }

        return hash_hmac('sha256', $body, $secret);
    }

    protected function hostIsSafe(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! $host) {
            return false;
        }

        $ips = filter_var($host, FILTER_VALIDATE_IP)
            ? [$host]
            : collect(@dns_get_record($host, DNS_A + DNS_AAAA))
                ->map(fn ($record) => $record['ip'] ?? $record['ipv6'] ?? null)
                ->filter()
                ->all();

        if (empty($ips)) {
            return false;
        }

        foreach ($ips as $ip) {
            $safe = filter_var(
                $ip,
                FILTER_VALIDATE_IP,
                FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
            );

            if ($safe === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, string>  $headers
     * @return array<string, string>
     */
    protected function redactHeaders(array $headers): array
    {
        $sensitive = ['authorization', 'cookie', 'x-portyard-signature-256', 'proxy-authorization'];

        foreach ($headers as $name => $value) {
            if (in_array(strtolower($name), $sensitive, true)) {
                $headers[$name] = '[redacted]';
            }
        }

        return $headers;
    }

    /**
     * @param  array<string, array<int, string>>  $headers
     * @return array<string, string>
     */
    protected function flattenHeaders(array $headers): array
    {
        $flat = [];

        foreach ($headers as $key => $value) {
            $flat[$key] = is_array($value) ? implode(', ', $value) : (string) $value;
        }

        return $flat;
    }
}
