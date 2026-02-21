@props(['command', 'output' => null, 'comment' => null])

<div class="grid grid-cols-[auto_1fr]">
    <span class="text-yellow-400 pr-2 row-start-2 font-semibold whitespace-nowrap ">λ ~</span>

    @isset($comment)
        <div class="text-neutral-600 row-start-1 col-start-2">
            # {{{ $comment }}}
        </div>
    @endif

    <span class="row-start-2 col-start-2">{{{ $command }}}</span>

    @isset($output)
        <div class="text-neutral-600 row-start-3 col-start-2">
            {{{ $output }}}
        </div>
    @endif
</div>
