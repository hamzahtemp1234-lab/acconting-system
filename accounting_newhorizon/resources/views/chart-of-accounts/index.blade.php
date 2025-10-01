@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <!-- الهيدر -->
    <header class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-3xl font-extrabold text-primary">
                <i class="fas fa-sitemap ml-3 text-secondary"></i> شجرة الحسابات
            </h1>
            <p class="text-sm text-gray-500 mt-1">استخدم الفلاتر أدناه لتصفية الجدول، ويمكن تطبيقها على الشجرة دون إعادة تحميل.</p>
        </div>

        <div class="flex space-x-3 space-x-reverse">
            <a href="{{ route('chart-of-accounts.export') }}"
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-medium">
                <i class="fas fa-file-excel ml-2"></i> تصدير Excel
            </a>

            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition font-medium flex items-center">
                <i class="fas fa-upload ml-2"></i> استيراد Excel
            </button>

            <button id="toggleView"
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-medium">
                <i class="fas fa-exchange-alt ml-2"></i> تبديل العرض
            </button>

            <a href="{{ route('chart-of-accounts.create', ['view' => request('view', 'table')]) }}"
                class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium">
                <i class="fas fa-plus ml-2"></i> إضافة حساب
            </a>
        </div>
    </header>

    <!-- بطاقات إحصائيات (حسب الفلاتر) -->
    <section class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
        <x-stat-card title="إجمالي" :value="$stats['total']" icon="fa-layer-group" color="blue" />
        <x-stat-card title="الرئيسي" :value="$stats['groups']" icon="fa-folder-tree" color="purple" />
        <x-stat-card title="الفرعية" :value="$stats['leaves']" icon="fa-file-invoice" color="yellow" />
        <x-stat-card title="نشط" :value="$stats['active']" icon="fa-check-circle" color="green" />
        <x-stat-card title="غير نشط" :value="$stats['inactive']" icon="fa-ban" color="red" />
        <x-stat-card title="مدين/دائن" :value="$stats['debit'].' / '.$stats['credit']" icon="fa-balance-scale" color="teal" />

    </section>


    <!-- فلاتر -->
    <form id="filtersForm" method="GET" action="{{ route('chart-of-accounts.index') }}" class="bg-white rounded-xl shadow p-4 mb-6">
        <div class="grid md:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">بحث (كود/اسم/وصف)</label>
                <input type="text" name="q" value="{{ $filters['q'] }}" class="w-full border rounded-lg px-3 py-2" placeholder="مثال: 1101 أو خزينة">
            </div>

            <div>
                <label class="block text-sm mb-1">الحالة</label>
                <select name="status" class="w-full border rounded-lg px-3 py-2">
                    <option value="">الكل</option>
                    <option value="نشط" {{ $filters['status']==='نشط' ? 'selected' : '' }}>نشط</option>
                    <option value="غير نشط" {{ $filters['status']==='غير نشط' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1">طبيعة الحساب</label>
                <select name="nature" class="w-full border rounded-lg px-3 py-2">
                    <option value="">الكل</option>
                    <option value="debit" {{ $filters['nature']==='debit' ? 'selected' : '' }}>مدين</option>
                    <option value="credit" {{ $filters['nature']==='credit' ? 'selected' : '' }}>دائن</option>
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1">نوع الحساب</label>
                <select name="account_type_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">الكل</option>
                    @foreach($accountTypes as $t)
                    <option value="{{ $t->id }}" {{ (string)$filters['account_type_id']===(string)$t->id ? 'selected' : '' }}>
                        {{ $t->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1">العملة</label>
                <select name="currency_id" class="w-full border rounded-lg px-3 py-2">
                    <option value="">الكل</option>
                    @foreach($currencies as $c)
                    <option value="{{ $c->id }}" {{ (string)$filters['currency_id']===(string)$c->id ? 'selected' : '' }}>
                        {{ $c->code }} - {{ $c->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1">هل مجموعة؟</label>
                <select name="is_group" class="w-full border rounded-lg px-3 py-2">
                    <option value="">الكل</option>
                    <option value="1" {{ $filters['is_group']==='1' ? 'selected' : '' }}>رئيسي</option>
                    <option value="0" {{ $filters['is_group']==='0' ? 'selected' : '' }}>فرعي</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90"><i class="fas fa-filter ml-2"></i> تطبيق على الجدول</button>
                <!-- <button type="button" id="applyToTree" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                    <i class="fas fa-tree ml-2"></i> تطبيق الفلاتر على الشجرة (بدون تحديث)
                </button> -->
                <a href="{{ route('chart-of-accounts.index', ['view' => $viewMode]) }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-100">
                    مسح الفلاتر
                </a>
            </div>
            <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" id="autoSubmit" checked>
                <span>تطبيق تلقائي عند تغيير الحقول</span>
            </label>
        </div>
    </form>

    <!-- مودال الاستيراد -->
    <div id="importModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
            <h2 class="text-lg font-bold mb-4">📂 استيراد ملف الحسابات</h2>
            <form action="{{ route('chart-of-accounts.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls,.csv" class="mb-4 w-full border p-2 rounded">
                <div class="flex justify-end space-x-2 space-x-reverse">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">إلغاء</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">استيراد</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ✅ عرض جدولي -->
    <div id="tableView" class="bg-white rounded-xl shadow-lg overflow-hidden {{ $viewMode==='tree' ? 'hidden' : '' }}">
        @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @endif
        @if(session('info'))
        <div class="bg-blue-100 text-blue-700 p-3 rounded mb-4">{{ session('info') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكود</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">اسم الحساب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطبيعة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($accountsTable as $account)
                    <tr class="hover:bg-gray-50 transition duration-150 {{ $account->trashed() ? 'opacity-60' : '' }} {{ !$account->is_group ? 'bg-yellow-50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $account->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($account->parent)
                            <span class="text-gray-500 text-sm">({{ $account->parent->name }}) → </span>
                            @endif
                            {{ $account->name }}
                        </td>
                        <td class="px-6 py-4">{{ $account->accountType->name ?? '---' }}</td>
                        <td class="px-6 py-4">{{ $account->nature === 'debit' ? 'مدين' : 'دائن' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $account->status == 'نشط' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $account->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium">
                            <a href="{{ route('chart-of-accounts.edit', $account->id) }}" class="text-secondary hover:text-primary ml-3">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                            <form action="{{ route('chart-of-accounts.destroy', $account->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 mr-3">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">لا توجد نتائج مطابقة.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50">
            {{ $accountsTable->links() }}
        </div>
    </div>

    <!-- ✅ عرض هرمي (شجرة) — بدون Pagination -->
    <div id="treeView" class="bg-white rounded-xl shadow-lg p-6 {{ $viewMode==='tree' ? '' : 'hidden' }}">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold"><i class="fas fa-tree ml-2 text-green-600"></i> العرض الهرمي</h2>
            <button id="toggleAllBtn" class="px-4 py-2 bg-secondary text-primary rounded-lg hover:bg-secondary/90 transition font-medium shadow">
                <i class="fas fa-minus-square ml-2"></i> طي الكل
            </button>
        </div>

        <ul class="space-y-2" id="treeRootUl">
            @foreach($treeRoots as $root)
            @include('chart-of-accounts.tree_node', ['account' => $root])
            @endforeach
        </ul>
        <div id="treeNoMatch" class="hidden text-center text-gray-500 mt-6">لا توجد نتائج مطابقة في الشجرة بعد تطبيق الفلاتر.</div>
    </div>
</main>

<!-- مودال الإضافة (كما هو لديك) -->
@include('chart-of-accounts._add_modal')

@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
        /* ======================== Helpers ======================== */
        const qs = (sel, root = document) => root.querySelector(sel);
        const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));
        const on = (el, ev, fn) => el && el.addEventListener(ev, fn);

        function getCsrf() {
            // 1) من meta (إن وجد)
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) return meta.getAttribute('content');
            // 2) من Blade متغير
            try {
                return "{{ csrf_token() }}";
            } catch (_) {}
            // 3) من أول input[name=_token]
            const t = document.querySelector('input[name=_token]');
            return t ? t.value : '';
        }

        async function fetchJSON(url, opts = {}) {
            const res = await fetch(url, opts);
            const ct = res.headers.get('content-type') || '';
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return ct.includes('application/json') ? res.json() : res.text();
        }

        function updateURLParam(key, val) {
            try {
                const url = new URL(window.location);
                url.searchParams.set(key, val);
                window.history.replaceState({}, '', url);
            } catch (_) {}
        }

        /* ======================== عناصر أساسية ======================== */
        const toggleBtn = qs('#toggleView');
        const tableView = qs('#tableView');
        const treeView = qs('#treeView');

        const filtersForm = qs('#filtersForm');
        const autoSubmit = qs('#autoSubmit');
        //const applyToTreeBtn = qs('#applyToTree');

        const treeRootUl = qs('#treeRootUl');
        const treeNoMatch = qs('#treeNoMatch');
        const toggleAllBtn = qs('#toggleAllBtn');

        // عناصر المودال
        const addModal = qs('#addAccountModal');
        const addForm = qs('#addAccountForm');
        const codeInput = qs('#code_modal');
        const nameInput = qs('#name_modal');
        const isGroupCheckbox = qs('#is_group_modal');
        const parentSelect = qs('#parent_id_modal_select');
        const accountTypeWrap = qs('#accountTypeWrapper_modal');
        const accountTypeSel = qs('#account_type_id_modal');
        const natureWrap = qs('#natureWrapper_modal');
        const natureSel = qs('#nature_modal');
        const statusSel = qs('#status_modal');
        const currencySel = qs('#currency_id_modal');

        /* ======================== تبديل العرض (جدول/شجرة) ======================== */
        // اضبط الحالة الابتدائية من ?view=...
        (function initViewFromURL() {
            if (!tableView || !treeView) return;
            try {
                const url = new URL(window.location);
                const current = url.searchParams.get('view'); // table | tree
                if (current === 'tree') {
                    tableView.classList.add('hidden');
                    treeView.classList.remove('hidden');
                } else if (current === 'table') {
                    tableView.classList.remove('hidden');
                    treeView.classList.add('hidden');
                }
            } catch (_) {}
        })();

        on(toggleBtn, 'click', function(e) {
            e.preventDefault();
            if (!tableView || !treeView) return;
            tableView.classList.toggle('hidden');
            treeView.classList.toggle('hidden');
            const mode = treeView.classList.contains('hidden') ? 'table' : 'tree';
            updateURLParam('view', mode);
        });



        /* ======================== فلاتر الشجرة (بدون رفرش) ======================== */
        function applyFiltersToTree() {
            if (!treeRootUl) return;

            const getVal = name => (qs(`[name="${name}"]`, filtersForm)?.value ?? '').trim();
            const q = (getVal('q') || '').toLowerCase();
            const status = getVal('status'); // 'نشط' | 'غير نشط' | ''
            const nature = getVal('nature'); // 'debit' | 'credit' | ''
            const is_group = getVal('is_group'); // '1' | '0' | ''
            const typeId = getVal('account_type_id');
            const currencyId = getVal('currency_id');

            const nodes = qsa('li[data-id]', treeRootUl);
            nodes.forEach(li => {
                const d = li.dataset;
                let ok = true;

                if (q) {
                    const txt = ((d.code || '') + ' ' + (d.name || '') + ' ' + (d.desc || '')).toLowerCase();
                    ok = ok && txt.includes(q);
                }
                if (status) ok = ok && (d.status === status);
                if (nature) ok = ok && (d.nature === nature);
                if (is_group !== '') ok = ok && (d.is_group === is_group);
                if (typeId) ok = ok && (d.type_id === typeId);
                if (currencyId) ok = ok && (d.currency_id === currencyId);

                li.classList.toggle('hidden-by-filter', !ok);
            });

            // إظهار رسالة لا يوجد نتائج / وإظهار سياق الآباء للعُقد المطابقة
            const anyVisible = !!qs('li[data-id]:not(.hidden-by-filter)', treeRootUl);
            if (treeNoMatch) treeNoMatch.classList.toggle('hidden', anyVisible);
            revealAncestorsForVisible();
        }

        function revealAncestorsForVisible() {
            const visibles = qsa('#treeRootUl li[data-id]:not(.hidden-by-filter)');
            visibles.forEach(li => {
                let parentUl = li.parentElement;
                while (parentUl && parentUl.id && parentUl.id.startsWith('children-')) {
                    parentUl.classList.remove('hidden'); // افتح فرع الأب
                    const parentLi = parentUl.parentElement; // li الأب
                    if (parentLi) {
                        const tgl = qs('.toggle-children', parentLi);
                        if (tgl) tgl.innerHTML = '<i class="fas fa-minus-square"></i>';
                    }
                    parentUl = parentLi ? parentLi.parentElement : null;
                }
            });
        }


        /* ======================== طي/توسيع الشجرة ======================== */
        // أزرار الطي/التوسيع الفردي موجودة داخل كل node (class="toggle-children")
        function bindNodeToggleButtons(scope = document) {
            qsa('.toggle-children', scope).forEach(btn => {
                on(btn, 'click', function() {
                    const targetId = this.dataset.target;
                    const ul = targetId ? qs('#' + targetId) : null;
                    if (!ul) return;
                    const isHidden = ul.classList.toggle('hidden');
                    this.innerHTML = isHidden ? '<i class="fas fa-plus-square"></i>' :
                        '<i class="fas fa-minus-square"></i>';
                });
            });
        }
        bindNodeToggleButtons();

        // زر طي/توسيع الكل
        let expanded = true; // افتراضي: مفتوح
        on(toggleAllBtn, 'click', function() {
            const allChildren = qsa("ul[id^='children-']");
            const allToggles = qsa(".toggle-children");
            expanded = !expanded;
            if (expanded) {
                allChildren.forEach(ul => ul.classList.remove('hidden'));
                allToggles.forEach(b => b.innerHTML = '<i class="fas fa-minus-square"></i>');
                this.innerHTML = '<i class="fas fa-minus-square ml-2"></i> طي الكل';
            } else {
                allChildren.forEach(ul => ul.classList.add('hidden'));
                allToggles.forEach(b => b.innerHTML = '<i class="fas fa-plus-square"></i>');
                this.innerHTML = '<i class="fas fa-plus-square ml-2"></i> توسيع الكل';
            }
        });

        /* ======================== مودال إضافة حساب ======================== */
        function openModal(parentId = null) {
            if (!addModal) return;
            addModal.classList.remove('hidden');
            if (parentSelect) parentSelect.value = parentId ?? '';
            // جلب الكود التالي
            const url = '/chart-of-accounts/next-code?parent_id=' + (parentId ?? '');
            fetchJSON(url).then(d => {
                    if (codeInput) codeInput.value = d.nextCode;
                })
                .catch(() => {});
        }

        function closeModal() {
            if (!addModal || !addForm) return;
            addModal.classList.add('hidden');
            addForm.reset();
            accountTypeWrap?.classList.remove('hidden');
            natureWrap?.classList.add('hidden');
        }
        // زر إغلاق (لو موجود)
        qsa('[data-close-modal="addAccountModal"]').forEach(b => on(b, 'click', closeModal));

        // ربط أزرار + (إضافة ابن) داخل الشجرة
        function bindAddButtons(scope = document) {
            qsa('.add-account-btn', scope).forEach(btn => {
                on(btn, 'click', () => openModal(btn.dataset.parent));
            });
        }
        bindAddButtons();

        // تبديل واجهة النوع/الطبيعة حسب "مجموعة؟"
        function toggleTypeNatureFields() {
            const isGroup = isGroupCheckbox && isGroupCheckbox.checked;
            if (accountTypeWrap) accountTypeWrap.classList.toggle('hidden', isGroup);
            if (natureWrap) natureWrap.classList.toggle('hidden', !isGroup);
            if (!isGroup) syncNatureFromType();
        }

        function syncNatureFromType() {
            const opt = accountTypeSel?.options[accountTypeSel.selectedIndex];
            const n = opt ? opt.getAttribute('data-nature') : '';
            if (n && natureSel) natureSel.value = n;
        }
        on(isGroupCheckbox, 'change', toggleTypeNatureFields);
        on(accountTypeSel, 'change', syncNatureFromType);

        // عند تغيير الأب في المودال → حدّث الكود
        on(parentSelect, 'change', function() {
            const pid = this.value || '';
            fetchJSON('/chart-of-accounts/next-code?parent_id=' + pid)
                .then(d => {
                    if (codeInput) codeInput.value = d.nextCode;
                })
                .catch(() => {});
        });

        // حفظ الحساب من المودال (Ajax)
        on(addForm, 'submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(addForm);
            try {
                const res = await fetch("{{ route('chart-of-accounts.store-tree') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrf()
                    },
                    body: formData
                });
                const result = await res.json();
                if (!result.success) throw new Error('failed');
                alert('✅ تم إضافة الحساب بنجاح');
                closeModal();

                // إدراج العقدة الجديدة في الشجرة
                const a = result.account;
                const nodeHTML = `
<li data-id="${a.id}"
    data-code="${a.code||''}"
    data-name="${a.name||''}"
    data-desc="${(a.description||'').replace(/"/g,'&quot;')}"
    data-status="${a.status||''}"
    data-nature="${a.nature||''}"
    data-is_group="${a.is_group ? '1':'0'}"
    data-type_id="${a.account_type_id ?? ''}"
    data-currency_id="${a.currency_id ?? ''}"
    class="border-r pr-4 border-gray-300">
  <div class="flex items-center justify-between py-1">
    <div class="flex items-center" style="padding-right: ${((a.level||1)-1)*20}px;">
      <span class="mr-2 w-4 inline-block"></span>
      <span class="font-bold ${a.is_group ? 'text-gray-800' : 'text-purple-600'}">${a.code}</span>
      <span class="mr-2 ${a.is_group ? 'text-gray-800' : 'text-purple-700 font-semibold'}">${a.name}</span>
      <span class="mr-2 text-sm ${a.is_group ? 'text-gray-500' : 'text-purple-400'}">(${a.nature==='debit'?'مدين':'دائن'})</span>
      <span class="mr-2 text-xs px-2 py-0.5 rounded-full ${a.status==='نشط'?'bg-green-100 text-green-700':'bg-red-100 text-red-700'}">${a.status||''}</span>
    </div>
    <div class="flex items-center ml-auto pr-1 space-x-2 space-x-reverse">
      ${a.is_group ? `<button type="button" class="text-green-500 hover:text-green-700 text-sm add-account-btn" data-parent="${a.id}">
        <i class="fas fa-plus-circle"></i></button>` : ''}
      <a href="/chart-of-accounts/${a.id}/edit" class="text-blue-500 hover:text-blue-700 text-sm"><i class="fas fa-edit"></i></a>
      <form action="/chart-of-accounts/${a.id}" method="POST" class="inline" onsubmit="return confirm('تأكيد حذف الحساب؟')">
        <input type="hidden" name="_token" value="${getCsrf()}">
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
      </form>
    </div>
  </div>
</li>`;

                if (a.parent_id) {
                    let parentUl = qs('#children-' + a.parent_id);
                    if (!parentUl) {
                        // أنشئ قائمة أبناء للأب إن لم توجد
                        const parentLi = qs(`li[data-id='${a.parent_id}']`);
                        if (parentLi) {
                            parentLi.insertAdjacentHTML('beforeend', `<ul id="children-${a.parent_id}" class="ml-6 mt-2 space-y-1 border-r pr-4 border-gray-200"></ul>`);
                            parentUl = qs('#children-' + a.parent_id);
                            // أضف زر toggle للأب (إن لم يكن موجودًا)
                            const btn = qs('.toggle-children', parentLi);
                            if (!btn) {
                                const titleDiv = qs('> .flex > .flex', parentLi);
                                if (titleDiv) {
                                    titleDiv.insertAdjacentHTML('afterbegin', `<button type="button" class="toggle-children text-gray-500 mr-2" data-target="children-${a.parent_id}">
                  <i class="fas fa-minus-square"></i></button>`);
                                }
                            }
                        }
                    }
                    parentUl?.insertAdjacentHTML('beforeend', nodeHTML);
                } else {
                    treeRootUl?.insertAdjacentHTML('beforeend', nodeHTML);
                }

                // أربط أزرار العقدة المضافة
                const justAdded = qs(`li[data-id='${a.id}']`);
                bindNodeToggleButtons(justAdded);
                bindAddButtons(justAdded);

            } catch (err) {
                console.error(err);
                alert('تعذر إضافة الحساب. تحقق من القيم.');
            }
        });

        // فتح المودال من أزرار موجودة مسبقًا
        bindAddButtons();

        // تبديل الحقول داخل المودال مبدئيًا
        toggleTypeNatureFields();
    });
</script>