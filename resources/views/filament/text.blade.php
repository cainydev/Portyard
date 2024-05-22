@props(['text', 'title' => null])

<div>
    @if($title)
        <h3>{{ $title }}</h3>
    @endif
    <p>{{ $text }}</p>
</div>
