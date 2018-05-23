@extends('Tagos::partials.shared')

@section('content')
    <div class="columns is-multiline">
        @foreach($models as $model => $value)
            @foreach($value as $item)
                <div class="column is-3">
                    <div class="card">
                        <div class="card-image link">
                            <figure class="image is-4by3">
                                <img src="//bulma.io/images/placeholders/1280x960.png" alt="Placeholder image">
                            </figure>
                        </div>
                        <div class="card-content">
                            {{-- date --}}
                            <p>{{ $item->created_at->format('F d, Y') }}</p>

                            <div class="content">
                                {{-- title --}}
                                <h3 class="title">{{ $item->title }}</h3>
                                <br>
                                {{-- tags --}}
                                @include('Tagos::partials.display', ['tags' => $one->tags])
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
@endsection