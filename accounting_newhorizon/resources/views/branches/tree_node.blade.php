<li data-id="{{ $branch->id }}" class="border-r pr-4 border-gray-300">
    <div class="flex items-center justify-between py-1">
        <div class="flex items-center" style="padding-right: {{ ($branch->level - 1) * 20 }}px;">
            <!-- أيقونة التوسيع -->
            @if($branch->children->count() > 0)
            <button type="button"
                class="toggle-children text-gray-500 hover:text-gray-700 ml-3"
                data-target="children-{{ $branch->id }}">
                <i class="fas fa-plus-square"></i>
            </button>
            @else
            <span class="w-4 mr-3"></span>
            @endif

            <!-- رقم واسم الحساب -->
            <span class="font-bold {{ $branch->is_group ? 'text-gray-800' : 'text-purple-600' }}">
                {{ $branch->code }}
            </span>
            <span class="mr-2 {{ $branch->is_group ? 'text-gray-800' : 'text-purple-700 font-semibold' }}">
                {{ $branch->name }}
            </span>
        </div>

        <!-- أزرار الإجراءات -->
        <div class="flex items-center ml-auto pr-1 space-x-2 space-x-reverse">
            @if($branch->children->count() > 0)
            <!-- زر الإضافة -->
            <button type="button"
                class="text-green-500 hover:text-green-700 text-sm add-branch-btn"
                data-parent="{{ $branch->id }}">
                <i class="fas fa-plus-circle"></i>
            </button>
            @endif

            <!-- زر التعديل -->
            <a href="{{ route('branches.edit', $branch->id) }}"
                class="text-blue-500 hover:text-blue-700 text-sm">
                <i class="fas fa-edit"></i>
            </a>

            <!-- زر الحذف -->
            <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" class="inline"
                onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700 text-sm">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </div>

    @if($branch->children->count() > 0)
    <ul id="children-{{ $branch->id }}" class="ml-6 mt-2 space-y-1 border-r pr-4 border-gray-200 hidden">
        @foreach($branch->children as $child)
        @include('branches.tree_node', ['branch' => $child])
        @endforeach
    </ul>
    @endif
</li>