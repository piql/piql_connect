@if (count($breadcrumbs))

    <div class="d-flex flex-row justify-content-start breadcrumbs">
        @foreach ($breadcrumbs as $breadcrumb)

            @if ($breadcrumb->url && !$loop->last)
                <a href="{{ $breadcrumb->url }}"><i class="breadcrumbs">{{ $breadcrumb->title }}</i></a>
                @if($loop->remaining > 1)
                    <i class="breadcrumbs"> > </i>
                @endif
            @endif

        @endforeach
    </div>

@endif
