<x-mail::message>
# You've Been Invited

{{ $inviterName }} has invited you to join **{{ $spaceName }}** as a **{{ $role }}**.

<x-mail::button :url="$acceptUrl">
Accept Invitation
</x-mail::button>

If you'd like to decline this invitation, you can [click here]({{ $declineUrl }}).

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
