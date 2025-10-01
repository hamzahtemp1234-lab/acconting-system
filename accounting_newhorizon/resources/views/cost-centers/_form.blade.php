<div class="space-y-6">
    <!-- كود -->
    <div>
        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">الكود</label>
        <input type="text" id="code" name="code"
            value="{{ old('code', $center->code ?? $nextCode ?? '') }}"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
            required>
    </div>

    <!-- الاسم -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم مركز التكلفة</label>
        <input type="text" id="name" name="name"
            value="{{ old('name', $center->name ?? '') }}"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
            required>
    </div>

    <!-- النوع -->
    <div>
        <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">نوع مركز التكلفة</label>
        <select id="type_id" name="type_id"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
            required>
            <option value="">اختر النوع</option>
            @foreach($types as $type)
            <option value="{{ $type->id }}" {{ old('type_id', $center->type_id ?? '') == $type->id ? 'selected' : '' }}>
                {{ $type->code }} - {{ $type->name }}
            </option>
            @endforeach
        </select>
    </div>

    <!-- الأب -->
    <div>
        <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">الأب</label>
        <select id="parent_id" name="parent_id"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition">
            <option value="">— لا يوجد (مستوى رئيسي) —</option>
            @foreach($parents as $parent)
            <option value="{{ $parent->id }}" {{ old('parent_id', $center->parent_id ?? '') == $parent->id ? 'selected' : '' }}>
                {{ $parent->code }} - {{ $parent->name }}
            </option>
            @endforeach
        </select>
    </div>

    <!-- مستوى -->
    <div>
        <label for="level" class="block text-sm font-medium text-gray-700 mb-2">المستوى</label>
        <input type="number" id="level" name="level"
            value="{{ old('level', $center->level ?? 1) }}"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition"
            min="1" required>
    </div>

    <!-- هل مجموعة -->
    <div class="flex items-center">
        <input type="checkbox" id="is_group" name="is_group" value="1"
            class="h-5 w-5 text-secondary border-gray-300 rounded"
            {{ old('is_group', $center->is_group ?? false) ? 'checked' : '' }}>
        <label for="is_group" class="mr-2 text-sm font-medium text-gray-700">هل هو مجموعة؟</label>
    </div>

    <!-- الحالة -->
    <div>
        <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
        <select id="is_active" name="is_active"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition">
            <option value="1" {{ old('is_active', $center->is_active ?? 1) == 1 ? 'selected' : '' }}>نشط</option>
            <option value="0" {{ old('is_active', $center->is_active ?? 1) == 0 ? 'selected' : '' }}>غير نشط</option>
        </select>
    </div>
</div>