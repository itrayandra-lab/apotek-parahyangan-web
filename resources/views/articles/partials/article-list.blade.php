@foreach($articles as $index => $article)
    <div data-article data-id="{{ $article->id }}">
        <x-article-card :article="$article" :index="$index" />
    </div>
@endforeach
