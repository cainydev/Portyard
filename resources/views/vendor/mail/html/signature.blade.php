@props([
    'name' => 'John',
    'title' => config('app.name'),
])
<table class="signature" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<p style="margin-bottom: 2px; color: #52525b; font-size: 15px;">Cheers,</p>
<p class="name" style="margin-bottom: 2px; color: #18181b; font-weight: 600; font-size: 15px;">{{ $name }}</p>
<p class="title" style="margin-bottom: 0; color: #a1a1aa; font-size: 13px;">{{ $title }}</p>
</td>
</tr>
</table>
