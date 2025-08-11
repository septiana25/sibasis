@if ($results['last_page'] > 1)
<nav class="mt-4">
    <ul class="pagination justify-content-center">

        {{-- Tombol Previous --}}
        <li class="page-item {{ $results['current_page'] == 1 ? 'disabled' : '' }}">
            <form action="{{ route($route) }}" method="post">
                @csrf
                <input type="hidden" name="page" value="{{ $results['current_page'] - 1 }}">
                @foreach ($params as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button class="page-link" type="submit" tabindex="-1">Previous</button>
            </form>
        </li>

        @php
            $start = max(1, $results['current_page'] - 2);
            $end = min($results['last_page'], $results['current_page'] + 2);
        @endphp

        @if ($start > 1)
            <li class="page-item">
                <form action="{{ route($route) }}" method="post">
                    @csrf
                    <input type="hidden" name="page" value="1">
                    @foreach ($params as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button class="page-link" type="submit">1</button>
                </form>
            </li>
            @if ($start > 2)
                <li class="page-item disabled"><span class="page-link">…</span></li>
            @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
            <li class="page-item">
                <form action="{{ route($route) }}" method="post">
                    @csrf
                    <input type="hidden" name="page" value="{{ $i }}">
                    @foreach ($params as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button class="page-link {{ $i == $results['current_page'] ? 'bg-dark text-white' : '' }}" type="submit">
                        {{ $i }}
                    </button>
                </form>
            </li>
        @endfor

        @if ($end < $results['last_page'])
            @if ($end < $results['last_page'] - 1)
                <li class="page-item disabled"><span class="page-link">…</span></li>
            @endif
            <li class="page-item">
                <form action="{{ route($route) }}" method="post">
                    @csrf
                    <input type="hidden" name="page" value="{{ $results['last_page'] }}">
                    @foreach ($params as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <button class="page-link" type="submit">{{ $results['last_page'] }}</button>
                </form>
            </li>
        @endif

        {{-- Tombol Next --}}
        <li class="page-item {{ $results['current_page'] == $results['last_page'] ? 'disabled' : '' }}">
            <form action="{{ route($route) }}" method="post">
                @csrf
                <input type="hidden" name="page" value="{{ $results['current_page'] + 1 }}">
                @foreach ($params as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button class="page-link" type="submit">Next</button>
            </form>
        </li>

    </ul>
</nav>
@endif
