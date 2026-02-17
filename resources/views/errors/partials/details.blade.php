<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight break-all">{{ $errorGroup->type }}</h2>
            <p class="text-slate-300 text-sm mt-1 font-mono break-all">{{ $errorGroup->message }}</p>
        </div>
        <div class="flex flex-col items-end gap-1">
            <div
                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-800 text-emerald-400 border border-emerald-500/20">
                {{ number_format($errorGroup->occurrences_count) }} Events
            </div>
            <div class="text-xs text-slate-500">
                Last seen {{ $errorGroup->last_seen_at->format('M j, Y g:i A') }}
            </div>
        </div>
    </div>

    <!-- Stack Trace -->
    <div class="bg-black/30 rounded-lg border border-white/5 overflow-hidden">
        <div class="px-4 py-2 border-b border-white/5 bg-white/5 flex justify-between items-center">
            <h3 class="text-sm font-medium text-white">Stack Trace</h3>
            <span
                class="text-xs text-slate-500 font-mono">{{ basename($errorGroup->file ?? '') }}:{{ $errorGroup->line }}</span>
        </div>
        <div class="p-4 max-h-[300px] overflow-y-auto">
            @php
                $latest = $recentOccurrences->first();
                $trace = $latest && isset($latest->payload['trace']) ? $latest->payload['trace'] : [];

                if (is_string($trace)) {
                    $decoded = json_decode($trace, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $trace = $decoded;
                    }
                }
             @endphp

            @if(is_array($trace) && count($trace) > 0)
                <div class="space-y-1 font-mono text-xs">
                    @foreach($trace as $index => $frame)
                        <div class="p-2 rounded hover:bg-white/5 transition-colors">
                            @if(is_array($frame))
                                <div class="flex justify-between items-start">
                                    <span
                                        class="text-emerald-400 break-all">{{ $frame['class'] ?? '' }}{{ $frame['function'] ?? '' }}</span>
                                    <span class="text-slate-500 whitespace-nowrap ml-2">Line {{ $frame['line'] ?? '' }}</span>
                                </div>
                                <div class="text-slate-500 mt-0.5 break-all">{{ $frame['file'] ?? '' }}</div>
                            @else
                                <div class="text-slate-300 break-all">{{ $frame }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif(is_string($trace))
                <div class="font-mono text-xs text-slate-300 whitespace-pre-wrap leading-relaxed">
                    {{ $trace }}
                </div>
            @else
                <div class="text-slate-500 italic text-sm">No stack trace available.</div>
            @endif
        </div>
    </div>

    <!-- Context -->
    @if($latest)
        <div class="bg-black/30 rounded-lg border border-white/5 overflow-hidden">
            <div class="px-4 py-2 border-b border-white/5 bg-white/5">
                <h3 class="text-sm font-medium text-white">Request Context</h3>
            </div>
            <div class="p-4 space-y-3 text-xs">
                @foreach($latest->payload as $key => $value)
                    @if(!in_array($key, ['trace', 'type', 'message', 'file', 'line']))
                        <div>
                            <span
                                class="block text-slate-500 uppercase tracking-wider mb-1">{{ str_replace('_', ' ', $key) }}</span>
                            <div class="font-mono text-slate-300 break-all bg-black/20 p-2 rounded">
                                @if(is_array($value) || is_object($value))
                                    {{ json_encode($value) }}
                                @else
                                    {{ $value }}
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>