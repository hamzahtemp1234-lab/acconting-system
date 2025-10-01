@extends('layouts.app')

@section('content')
<main class="flex-1 p-8 overflow-y-auto">

    <header class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-extrabold text-primary">
            <i class="fas fa-cog ml-3 text-secondary"></i> إعدادات الحسابات (الوكلاء + الموظفين + البنوك + الصناديق)
        </h1>
        <a href="{{ route('system-settings.index') }}"
            class="px-4 py-2 border border-gray-300 rounded-lg text-primary hover:bg-gray-100 transition font-medium">
            <i class="fas fa-arrow-right ml-2"></i> الرجوع
        </a>
    </header>

    @if(session('success'))
    <div class="mb-6 px-4 py-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="mb-6 px-4 py-3 bg-red-100 text-red-800 rounded">
        @foreach($errors->all() as $err)
        <div>• {{ $err }}</div>
        @endforeach
    </div>
    @endif

    {{-- Tabs --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex gap-3" id="tabs">
            <button data-tab="agents" class="px-4 py-2 rounded-t-lg border border-b-0 bg-white  text-primary font-bold">الوكلاء</button>
            <button data-tab="employees" class="px-4 py-2 rounded-t-lg border border-b-0 bg-gray-50 text-gray-600">الموظفون</button>
            <button data-tab="banks" class="px-4 py-2 rounded-t-lg border border-b-0 bg-gray-50 text-gray-600">البنوك</button>
            <button data-tab="cashboxes" class="px-4 py-2 rounded-t-lg border border-b-0 bg-gray-50 text-gray-600">الصناديق</button>
        </nav>
    </div>

    {{-- Agents --}}
    <section id="tab-agents" class="bg-white rounded-b-xl rounded-tr-xl shadow p-8 space-y-8">
        <form action="{{ route('agents-settings.update') }}" method="POST" class="space-y-8">
            @csrf @method('PUT')

            <section>
                <h2 class="text-lg font-bold mb-2">إنشاء الحساب تلقائياً (الوكلاء)</h2>
                <label class="inline-flex items-center space-x-2 space-x-reverse">
                    <input type="hidden" name="auto_create" value="0">
                    <input type="checkbox" name="auto_create" value="1" {{ $agentsAuto ? 'checked' : '' }}>
                    <span>تفعيل إنشاء حساب فرعي تلقائي للوكلاء</span>
                </label>
                <p class="text-xs text-gray-500 mt-1">إن فُعّل، يُنشأ حساب Leaf باسم الوكيل تحت الأب المختار.</p>
            </section>

            <section>
                <h2 class="text-lg font-bold mb-2">الحساب الأب (مجموعة)</h2>
                <select name="parent_id" class="w-full border rounded-lg px-4 py-2">
                    <option value="">— اختر حساب أب (مجموعة) —</option>
                    @foreach($groupAccounts as $g)
                    <option value="{{ $g->id }}" {{ (string)$agentsParentId === (string)$g->id ? 'selected' : '' }}>
                        {{ $g->code }} - {{ $g->name }}
                    </option>
                    @endforeach
                </select>
            </section>

            <div class="flex justify-end">
                <button class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold shadow">
                    <i class="fas fa-save ml-2"></i> حفظ إعدادات الوكلاء
                </button>
            </div>
        </form>
    </section>

    {{-- Employees --}}
    <section id="tab-employees" class="hidden bg-white rounded-b-xl rounded-tr-xl shadow p-8 space-y-8">
        <form action="{{ route('employee-settings.update') }}" method="POST" class="space-y-8">
            @csrf @method('PUT')

            <section>
                <h2 class="text-lg font-bold mb-2">إنشاء الحساب تلقائياً (الموظفون)</h2>
                <label class="inline-flex items-center space-x-2 space-x-reverse">
                    <input type="hidden" name="auto_create" value="0">
                    <input type="checkbox" name="auto_create" value="1" {{ $employeesAuto ? 'checked' : '' }}>
                    <span>تفعيل إنشاء حساب فرعي تلقائي للموظفين</span>
                </label>
            </section>

            <section>
                <h2 class="text-lg font-bold mb-2">الحساب الأب (مجموعة)</h2>
                <select name="parent_id" class="w-full border rounded-lg px-4 py-2">
                    <option value="">— اختر حساب أب (مجموعة) —</option>
                    @foreach($groupAccounts as $g)
                    <option value="{{ $g->id }}" {{ (string)$employeesParentId === (string)$g->id ? 'selected' : '' }}>
                        {{ $g->code }} - {{ $g->name }}
                    </option>
                    @endforeach
                </select>
            </section>

            <div class="flex justify-end">
                <button class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold shadow">
                    <i class="fas fa-save ml-2"></i> حفظ إعدادات الموظفين
                </button>
            </div>
        </form>
    </section>

    {{-- Banks --}}
    <section id="tab-banks" class="hidden bg-white rounded-b-xl rounded-tr-xl shadow p-8 space-y-8">
        <form action="{{ route('banks-settings.update') }}" method="POST" class="space-y-8">
            @csrf @method('PUT')

            <section>
                <h2 class="text-lg font-bold mb-2">إنشاء الحساب تلقائياً (البنوك)</h2>
                <label class="inline-flex items-center space-x-2 space-x-reverse">
                    <input type="hidden" name="auto_create" value="0">
                    <input type="checkbox" name="auto_create" value="1" {{ $banksAuto ? 'checked' : '' }}>
                    <span>تفعيل إنشاء حساب فرعي تلقائي للبنوك</span>
                </label>
            </section>

            <section>
                <h2 class="text-lg font-bold mb-2">الحساب الأب (مجموعة)</h2>
                <select name="parent_id" class="w-full border rounded-lg px-4 py-2">
                    <option value="">— اختر حساب أب (مجموعة) —</option>
                    @foreach($groupAccounts as $g)
                    <option value="{{ $g->id }}" {{ (string)$banksParentId === (string)$g->id ? 'selected' : '' }}>
                        {{ $g->code }} - {{ $g->name }}
                    </option>
                    @endforeach
                </select>
            </section>

            <div class="flex justify-end">
                <button class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold shadow">
                    <i class="fas fa-save ml-2"></i> حفظ إعدادات البنوك
                </button>
            </div>
        </form>
    </section>

    {{-- Cashboxes --}}
    <section id="tab-cashboxes" class="hidden bg-white rounded-b-xl rounded-tr-xl shadow p-8 space-y-8">
        <form action="{{ route('cashboxes-settings.update') }}" method="POST" class="space-y-8">
            @csrf @method('PUT')

            <section>
                <h2 class="text-lg font-bold mb-2">إنشاء الحساب تلقائياً (الصناديق)</h2>
                <label class="inline-flex items-center space-x-2 space-x-reverse">
                    <input type="hidden" name="auto_create" value="0">
                    <input type="checkbox" name="auto_create" value="1" {{ $cashboxesAuto ? 'checked' : '' }}>
                    <span>تفعيل إنشاء حساب فرعي تلقائي للصناديق</span>
                </label>
            </section>

            <section>
                <h2 class="text-lg font-bold mb-2">الحساب الأب (مجموعة)</h2>
                <select name="parent_id" class="w-full border rounded-lg px-4 py-2">
                    <option value="">— اختر حساب أب (مجموعة) —</option>
                    @foreach($groupAccounts as $g)
                    <option value="{{ $g->id }}" {{ (string)$cashboxesParentId === (string)$g->id ? 'selected' : '' }}>
                        {{ $g->code }} - {{ $g->name }}
                    </option>
                    @endforeach
                </select>
            </section>

            <div class="flex justify-end">
                <button class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold shadow">
                    <i class="fas fa-save ml-2"></i> حفظ إعدادات الصناديق
                </button>
            </div>
        </form>
    </section>

</main>
<script>
    (function() {
        const tabs = document.querySelectorAll('#tabs [data-tab]');
        const panes = {
            agents: document.getElementById('tab-agents'),
            employees: document.getElementById('tab-employees'),
            banks: document.getElementById('tab-banks'),
            cashboxes: document.getElementById('tab-cashboxes'),
        };

        function activate(key) {
            tabs.forEach(b => {
                const is = b.getAttribute('data-tab') === key;
                b.classList.toggle('bg-white', is);
                b.classList.toggle('text-primary', is);
                b.classList.toggle('font-bold', is);
                b.classList.toggle('bg-gray-50', !is);
                b.classList.toggle('text-gray-600', !is);
            });
            Object.entries(panes).forEach(([k, el]) => {
                el.classList.toggle('hidden', k !== key);
            });
        }

        tabs.forEach(btn => {
            btn.addEventListener('click', () => activate(btn.getAttribute('data-tab')));
        });

        const params = new URLSearchParams(location.search);
        const tab = params.get('tab');
        activate(['employees', 'banks', 'cashboxes'].includes(tab) ? tab : 'agents');
    })();
</script>
@endsection