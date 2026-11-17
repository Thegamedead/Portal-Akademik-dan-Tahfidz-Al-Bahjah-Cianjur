{{--
    Component: untaian-progres
    Visualisasi progres hafalan 30 juz dalam bentuk untaian bead/titik berwarna.

    Props:
    - capaianJuzs  : array per-juz dengan keys: juz (int 1-30), status (tuntas|berjalan|belum)
    - targetJuzs   : array of juz numbers that are configured as targets, e.g. [28, 29, 30]
    - targetKelulusan : int jumlah juz target (kept for backward compat, not used for ring)
    - size         : "sm" | "md" | "lg" (default: "md")
--}}
@props(["capaianJuzs" => [], "targetJuzs" => [], "targetKelulusan" => 30, "size" => "md"])

@php
    $statusMap = [];
    if (is_array($capaianJuzs)) {
        foreach ($capaianJuzs as $item) {
            $juzNum = is_array($item) ? ($item["juz"] ?? null) : ($item->juz ?? null);
            $status  = is_array($item) ? ($item["status"] ?? "belum") : ($item->status ?? "belum");
            if ($juzNum) {
                $statusMap[$juzNum] = $status;
            }
        }
    }

    // Build a lookup for target juz
    $targetJuzSet = array_flip((array) $targetJuzs);

    $sizes = [
        "sm" => ["dot" => "w-5 h-5", "text" => "text-[9px]",  "gap" => "gap-1"],
        "md" => ["dot" => "w-7 h-7", "text" => "text-[10px]", "gap" => "gap-1.5"],
        "lg" => ["dot" => "w-8 h-8", "text" => "text-[11px]", "gap" => "gap-2"],
    ];
    $s = $sizes[$size] ?? $sizes["md"];
@endphp

<div class="w-full">
    {{-- Legend --}}
    <div class="flex flex-wrap items-center gap-4 mb-3 text-xs" style="color:#6B7280;">
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full" style="background-color:#1B5E4F;"></div>
            <span>Tuntas</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full" style="background-color:#C9A227;"></div>
            <span>Sedang Berjalan</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full" style="background-color:#E5E7EB; border:1px solid #D1D5DB;"></div>
            <span>Belum</span>
        </div>
        @if(!empty($targetJuzs))
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded-full" style="background-color:#E5E7EB; border:2px solid #C9A227;"></div>
            <span style="color:#A16207;">Target (Juz {{ implode(', ', $targetJuzs) }})</span>
        </div>
        @endif
    </div>

    {{-- Juz Grid --}}
    <div class="flex flex-wrap {{ $s["gap"] }}">
        @for ($juz = 1; $juz <= 30; $juz++)
            @php
                $status   = $statusMap[$juz] ?? "belum";
                $isTarget = isset($targetJuzSet[$juz]);

                if ($status === "tuntas") {
                    $dotStyle = "background-color:#1B5E4F; color:#FFFFFF;";
                } elseif ($status === "berjalan") {
                    $dotStyle = "background-color:#C9A227; color:#FFFFFF;";
                } else {
                    $dotStyle = "background-color:#E5E7EB; color:#9CA3AF; border:1px solid #D1D5DB;";
                }

                // Ring only around configured target juz
                $targetRing = $isTarget
                    ? "box-shadow:0 0 0 2px #C9A227, 0 0 0 4px rgba(201,162,39,0.25);"
                    : "";
            @endphp
            <div
                class="{{ $s["dot"] }} rounded-full flex items-center justify-center font-bold transition-transform hover:scale-125 cursor-default flex-shrink-0"
                style="{{ $dotStyle }}{{ $targetRing }}"
                title="Juz {{ $juz }} - {{ ucfirst($status) }}{{ $isTarget ? ' (Target)' : '' }}"
            >
                <span class="{{ $s["text"] }}">{{ $juz }}</span>
            </div>
        @endfor
    </div>

    @php
        $capaianCollection = collect($capaianJuzs);
        $totalTuntas   = $capaianCollection->filter(fn($j) => (is_array($j) ? ($j["status"] ?? "") : ($j->status ?? "")) === "tuntas")->count();
        $totalBerjalan = $capaianCollection->filter(fn($j) => (is_array($j) ? ($j["status"] ?? "") : ($j->status ?? "")) === "berjalan")->count();
    @endphp
    <div class="flex flex-wrap gap-5 mt-4 pt-3 border-t text-xs" style="border-color:#F3F4F6; color:#6B7280;">
        <span class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color:#1B5E4F;"></span>
            <strong style="color:#1B5E4F;">{{ $totalTuntas }}</strong>&nbsp;Juz Tuntas
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color:#C9A227;"></span>
            <strong style="color:#C9A227;">{{ $totalBerjalan }}</strong>&nbsp;Juz Berjalan
        </span>
        <span class="flex items-center gap-1.5">
            <span class="w-2.5 h-2.5 rounded-full inline-block" style="background-color:#E5E7EB; border:1px solid #D1D5DB;"></span>
            <strong style="color:#374151;">{{ 30 - $totalTuntas - $totalBerjalan }}</strong>&nbsp;Juz Belum
        </span>
    </div>
</div>
