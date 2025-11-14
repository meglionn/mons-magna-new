<div 
x-show="showDialog" 
  x-cloak 
  x-transition.opacity.duration.200ms
  @keydown.escape.window="showDialog = false"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">

  {{-- Box isi modal --}}
  <div 
    @click.outside="$parent.showDialog = false" 
    class="bg-white rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto p-6">

    {{-- Header --}}
    <div class="flex justify-between items-center border-b pb-4 mb-4">
      <div>
        <h2 class="text-2xl font-semibold">Buat Pesanan Custom</h2>
        <p class="text-gray-500 text-sm">Isi detail pelanggan, produk, ukuran, dan pembayaran</p>
      </div>
      <button 
        @click="$parent.showDialog = false" 
        class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
    </div>

    {{-- Tabs --}}
    <div x-data="{ tab: 'customer' }">
      <div class="grid grid-cols-4 border-b mb-6">
        <button @click="tab = 'customer'" :class="tab === 'customer' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
          class="py-2 font-medium text-center">Customer</button>
        <button @click="tab = 'product'" :class="tab === 'product' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
          class="py-2 font-medium text-center">Product</button>
        <button @click="tab = 'measurements'" :class="tab === 'measurements' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
          class="py-2 font-medium text-center">Measurements</button>
        <button @click="tab = 'payment'" :class="tab === 'payment' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-600'"
          class="py-2 font-medium text-center">Payment</button>
      </div>

      {{-- CUSTOMER TAB --}}
      <div x-show="tab === 'customer'" class="space-y-4">
        <div class="grid gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Order Number</label>
            <input type="text" class="w-full border rounded-lg p-2" placeholder="CO-2025-001">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Customer Name</label>
            <input type="text" class="w-full border rounded-lg p-2" placeholder="John Doe">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Email</label>
              <input type="email" class="w-full border rounded-lg p-2" placeholder="john@example.com">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Phone</label>
              <input type="text" class="w-full border rounded-lg p-2" placeholder="08123456789">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Order Date</label>
              <input type="date" class="w-full border rounded-lg p-2">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Due Date</label>
              <input type="date" class="w-full border rounded-lg p-2">
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Status</label>
              <select class="w-full border rounded-lg p-2">
                <option>Pending</option>
                <option>Confirmed</option>
                <option>In Production</option>
                <option>Quality Check</option>
                <option>Ready</option>
                <option>Delivered</option>
                <option>Cancelled</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Priority</label>
              <select class="w-full border rounded-lg p-2">
                <option>Normal</option>
                <option>Urgent</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      {{-- PRODUCT TAB --}}
      <div x-show="tab === 'product'" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Product Type</label>
          <select class="w-full border rounded-lg p-2">
            <option>Formal Oxford Shoes</option>
            <option>Derby Shoes</option>
            <option>Loafers</option>
            <option>Monk Strap Shoes</option>
            <option>Chelsea Boots</option>
            <option>Chukka Boots</option>
            <option>Sandals</option>
          </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Size</label>
            <input type="text" class="w-full border rounded-lg p-2" placeholder="42 EU / 9 US">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Color</label>
            <select class="w-full border rounded-lg p-2">
              <option>Black</option>
              <option>Dark Brown</option>
              <option>Tan</option>
              <option>Custom Color</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Material</label>
          <select class="w-full border rounded-lg p-2">
            <option>Full Grain Leather</option>
            <option>Suede Leather</option>
            <option>Nubuck Leather</option>
            <option>Patent Leather</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Style</label>
          <input type="text" class="w-full border rounded-lg p-2" placeholder="Oxford Cap Toe, Brogue, etc.">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Custom Features</label>
          <textarea class="w-full border rounded-lg p-2" rows="3" placeholder="Describe custom features or details..."></textarea>
        </div>
      </div>

      {{-- MEASUREMENTS TAB --}}
      <div x-show="tab === 'measurements'" class="space-y-4">
        <p class="text-gray-600 text-sm">Optional: Add custom measurements for perfect fit</p>
        <div class="grid grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Foot Length</label>
            <input type="text" class="w-full border rounded-lg p-2" placeholder="27.5 cm">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Foot Width</label>
            <input type="text" class="w-full border rounded-lg p-2" placeholder="10.2 cm">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Instep Height</label>
            <input type="text" class="w-full border rounded-lg p-2" placeholder="8.5 cm">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Special Requirements</label>
          <textarea class="w-full border rounded-lg p-2" rows="3" placeholder="Wide fit, high arch, etc."></textarea>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Additional Notes</label>
          <textarea class="w-full border rounded-lg p-2" rows="3" placeholder="Any other notes or requests..."></textarea>
        </div>
      </div>

      {{-- PAYMENT TAB --}}
      <div x-show="tab === 'payment'" class="space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Total Price (IDR)</label>
          <input type="number" class="w-full border rounded-lg p-2" placeholder="1200000">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Deposit Paid (IDR)</label>
          <input type="number" class="w-full border rounded-lg p-2" placeholder="600000">
        </div>
        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
          <div class="flex justify-between text-sm text-gray-700">
            <span>Total Price:</span><span>IDR 1.200.000</span>
          </div>
          <div class="flex justify-between text-sm text-gray-700">
            <span>Deposit Paid:</span><span class="text-green-600">IDR 600.000</span>
          </div>
          <div class="border-t pt-2 flex justify-between font-semibold">
            <span>Balance Due:</span><span class="text-red-600">IDR 600.000</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Footer --}}
    <div class="mt-6 flex justify-end gap-2 border-t pt-4">
      <button 
        @click="showDialog = false" 
        class="border rounded-lg px-4 py-2 hover:bg-gray-100">
        Cancel
      </button>
      <button class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700">
        Save Order
      </button>
    </div>

  </div>
</div>