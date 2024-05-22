@php($name = auth()->user()->namespace . '/' . (str($getState()['name'])->trim()->length() == 0 ? 'new-repo' : $getState()['name']))

<div class="flex flex-col gap-2">
    <h4 class="text-2xl font-semibold">Pushing images</h4>
    <div class="flex flex-col gap-2 items-stretch max-w-min">
        <p>You can push a new image to this repository using the CLI:</p>
        <pre class="p-3 border bg-gray-800 border-gray-500 rounded max-w-min"><div><div>docker tag local-image:tagname {{ $name }}:tagname</div><div>docker push dockermeister.de/{{ $name }}:tagname</div></div></pre>
        <p>Make sure to replace <code class="font-mono">tagname</code>
            with your desired image repository tag.</p></div>
</div>
