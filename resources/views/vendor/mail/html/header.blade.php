@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === config('app.name'))
<img src="{{ asset('img/logo.png') }}" class="logo" alt="{{ config('app.name') }} Logo" style="max-width: 150px;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>