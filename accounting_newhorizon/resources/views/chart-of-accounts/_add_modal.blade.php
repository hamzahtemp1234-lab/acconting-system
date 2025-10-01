 <!-- ✅ Modal إضافة حساب -->
 <div id="addAccountModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
     <div class="bg-white rounded-xl shadow-2xl border-t-4 border-primary w-full max-w-4xl p-8 relative">

         <button type="button" onclick="closeModal()" class="absolute top-3 left-3 text-gray-500 hover:text-gray-800">
             <i class="fas fa-times text-xl"></i>
         </button>

         <h2 class="text-2xl font-bold mb-6 text-primary">
             <i class="fas fa-plus-circle ml-2 text-secondary"></i> إضافة حساب جديد
         </h2>

         <form id="addAccountForm">
             @csrf
             <input type="hidden" name="parent_id" id="parent_id_modal">

             <div class="grid grid-cols-2 gap-6">
                 <div>
                     <label class="block text-sm font-medium text-gray-700 mb-2">رقم الحساب</label>
                     <input type="text" id="code_modal" name="code" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" readonly>
                 </div>

                 <div>
                     <label class="block text-sm font-medium text-gray-700 mb-2">اسم الحساب <span class="text-red-500">*</span></label>
                     <input type="text" id="name_modal" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition" required>
                 </div>

                 <div class="col-span-2 flex items-center">
                     <input type="checkbox" id="is_group_modal" name="is_group" value="1" class="h-5 w-5 text-secondary border-gray-300 rounded">
                     <label for="is_group_modal" class="mr-2 text-sm font-medium text-gray-700">هل هو مجموعة؟</label>
                 </div>

                 <div>
                     <label class="block text-sm font-medium text-gray-700 mb-2">الحساب الأب</label>
                     <select id="parent_id_modal_select" name="parent_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-secondary focus:border-secondary transition">
                         <option value="">— لا يوجد (حساب رئيسي) —</option>
                         @foreach($allGroupParents as $parent)
                         <option value="{{ $parent->id }}">{{ $parent->code }} - {{ $parent->name }}</option>
                         @endforeach
                     </select>
                 </div>

                 <div id="accountTypeWrapper_modal">
                     <label class="block text-sm font-medium text-gray-700 mb-2">نوع الحساب</label>
                     <select id="account_type_id_modal" name="account_type_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                         <option value="">اختر نوع الحساب</option>
                         @foreach($accountTypes as $type)
                         <option value="{{ $type->id }}" data-nature="{{ $type->nature }}">
                             {{ $type->name }} ({{ $type->nature }})
                         </option>
                         @endforeach
                     </select>
                 </div>

                 <div id="natureWrapper_modal" class="hidden">
                     <label class="block text-sm font-medium text-gray-700 mb-2">طبيعة الحساب</label>
                     <select id="nature_modal" name="nature" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                         <option value="debit">مدين</option>
                         <option value="credit">دائن</option>
                     </select>
                 </div>

                 <div>
                     <label class="block text-sm font-medium text-gray-700 mb-2">العملة</label>
                     <select id="currency_id_modal" name="currency_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                         <option value="">اختر العملة</option>
                         @foreach($currencies as $currency)
                         <option value="{{ $currency->id }}">{{ $currency->name }} ({{ $currency->code }})</option>
                         @endforeach
                     </select>
                 </div>

                 <div>
                     <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                     <select id="status_modal" name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                         <option value="نشط">نشط</option>
                         <option value="غير نشط">غير نشط</option>
                     </select>
                 </div>
             </div>

             <div class="flex justify-end mt-8 space-x-4 space-x-reverse">
                 <button type="button" onclick="closeModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">إلغاء</button>
                 <button type="submit" class="px-6 py-3 bg-secondary text-primary rounded-lg hover:bg-secondary/90 font-bold">
                     <i class="fas fa-save ml-2"></i> حفظ
                 </button>
             </div>
         </form>
     </div>
 </div>