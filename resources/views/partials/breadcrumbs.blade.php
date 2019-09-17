@if (count($breadcrumbs))

    <div class="d-flex flex-row justify-content-start breadcrumbs">
        @foreach ($breadcrumbs as $breadcrumb)

            @if ($breadcrumb->url && !$loop->last)
                <a href="{{ $breadcrumb->url }}"><i class="breadcrumbs">{{ $breadcrumb->title }}</i></a>
                <i class="breadcrumbs"> > </i>
            @elseif ($breadcrumb->url && $loop->last)
                <i class="breadcrumbs disabledIcon">{{ $breadcrumb->title }}</i>
            @endif

        @endforeach
    </div>

@endif
