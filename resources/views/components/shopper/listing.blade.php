<x-table-column>
    <x-shopper.status :shopper="$shopper"/>
</x-table-column>

<x-table-column>
    {{ $shopper['first_name'] }} {{ $shopper['last_name'] }}
</x-table-column>

<x-table-column>
    {{ $shopper['email'] }}
</x-table-column>

<x-table-column>
    {{ $shopper['check_in'] }}
</x-table-column>

<x-table-column>
    @if( isset($shopper['status']['name']) && $shopper['status']['name'] == 'Active' )
        <div>
            <form method="post" action="{{ route('store.location.checkout', [
                'locationUuid' => $location['uuid'],
                'storeUuid' => $storeUuid,
            ]) }}">


                @csrf
                <input type="hidden" id="shopper_id" name="shopper_id" value="{{$shopper['uuid']}}"/>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase transition">Check Out</button>
            </form>
        </div>
    @else
        {{ $shopper['check_out'] }}
    @endif

</x-table-column>

{{--<x-table-column>--}}

{{--</x-table-column>--}}
