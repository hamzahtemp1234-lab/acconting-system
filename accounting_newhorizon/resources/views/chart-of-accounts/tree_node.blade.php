<li data-id="{{ $account->id }}" class="border-r pr-4 border-gray-300">
    <div class="flex items-center justify-between py-1">
        <div class="flex items-center" style="padding-right: {{ ($account->level - 1) * 20 }} px;">
            @if($account->childrenRecursive->count())
            <button type="button" class="toggle-children text-gray-500 mr-2" data-target="children-{{ $account->id }}">
                <i class="fas fa-minus-square"></i>
            </button>
            @else
            <span class="mr-2 w-4 inline-block"></span>
            @endif

            <span class="font-bold {{ $account->is_group ? 'text-gray-800' : 'text-purple-600' }}">{{ $account->code }}</span>
            <span class="mr-2 {{ $account->is_group ? 'text-gray-800' : 'text-purple-700 font-semibold' }}">{{ $account->name }}</span>
            <span class="mr-2 text-sm {{ $account->is_group ? 'text-gray-500' : 'text-purple-400' }}">({{ $account->nature === 'debit' ? 'مدين' : 'دائن' }})</span>
            <span class="mr-2 text-xs px-2 py-0.5 rounded-full {{ $account->status=='نشط' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $account->status }}</span>
        </div>

        <div class="flex items-center ml-auto pr-1 space-x-2 space-x-reverse">
            @if($account->is_group)
            <button type="button" class="text-green-500 hover:text-green-700 text-sm add-account-btn" data-parent="{{ $account->id }}">
                <i class="fas fa-plus-circle"></i>
            </button>
            @endif
            <a href="{{ route('chart-of-accounts.edit', $account->id) }}" class="text-blue-500 hover:text-blue-700 text-sm">
                <i class="fas fa-edit"></i>
            </a>
            <form action="{{ route('chart-of-accounts.destroy', $account->id) }}" method="POST" class="inline" onsubmit="return confirm('تأكيد حذف الحساب؟')">
                @csrf @method('DELETE')
                <button class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
            </form>
        </div>
    </div>

    @if($account->childrenRecursive->count())
    <ul id="children-{{ $account->id }}" class="ml-6 mt-2 space-y-1 border-r pr-4 border-gray-200">
        @foreach($account->childrenRecursive as $child)
        @include('chart-of-accounts.tree_node', ['account' => $child])
        @endforeach
    </ul>
    @endif
</li>