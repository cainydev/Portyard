@php($repo = $getState())

<div class="flex flex-col gap-2">
    <h4 class="text-2xl font-semibold">Docker commands</h4>
    <div class="flex flex-col gap-2 items-stretch max-w-min">
        <p>To push a new tag to this repository:</p>
        <pre
            class="p-3 border bg-gray-800 border-gray-500 rounded max-w-min">docker push {{config('app.url')}}/{{$repo['namespace']}}/{{$repo['name']}}:tagname</pre>
        <p>Make sure to replace <code class="font-mono">tagname</code>
            with your desired image repository tag.</p></div>
</div>
