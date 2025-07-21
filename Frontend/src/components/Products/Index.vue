<template>
  <div class="p-6">
    <!-- Header Section -->
    <div class="mb-6">
      <h1 class="text-3xl font-bold text-gray-800 mb-2">Products</h1>
      <p class="text-gray-600">Discover our amazing collection of products</p>
    </div>

    <!-- Filters Section -->
    <Card class="mb-6">
      <template #content>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
          <!-- Search -->
          <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">Search Products</label>
            <InputText v-model="filters.search" placeholder="Search products..." class="w-full"
              @input="debounceSearch" />
          </div>

          <!-- Category Filter -->
          <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">Category</label>
            <Dropdown v-model="filters.category" :options="categoryOptions" optionLabel="label" optionValue="value"
              placeholder="All Categories" class="w-full" @change="fetchProducts" showClear />
          </div>

          <!-- Min Price -->
          <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">Min Price</label>
            <InputNumber v-model="filters.min_price" placeholder="0" :min="0" currency="IDR" locale="id-ID"
              @input="debounceSearch" />
          </div>

          <!-- Max Price -->
          <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">Max Price</label>
            <InputNumber v-model="filters.max_price" placeholder="1000000" :min="0" currency="IDR" locale="id-ID"
              @input="debounceSearch" />
          </div>

          <!-- Sort -->
          <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 mb-2">Sort By</label>
            <Dropdown v-model="filters.sort" :options="sortOptions" optionLabel="label" optionValue="value"
              placeholder="Default" class="w-full" @change="fetchProducts" />
          </div>
        </div>

        <!-- Filter Actions -->
        <div class="flex gap-2 mt-4">
          <Button label="Clear Filters" icon="pi pi-times" outlined size="small" @click="clearFilters" />
          <Button label="Apply Filters" icon="pi pi-search" size="small" @click="fetchProducts" />
        </div>
      </template>
    </Card>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-8">
      <ProgressSpinner />
    </div>

    <!-- Products Grid -->
    <div v-else-if="products.length > 0"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
      <Card v-for="product in products" :key="product.id" class="product-card">
        <template #header>
          <div class="relative">
            <img :src="product.main_image || 'https://picsum.photos/300/200?random=' + product.id"
              :alt="product.product_name" class="w-full h-48 object-cover" />
            <div class="absolute top-2 right-2">
              <Tag :value="product.category?.category_name" class="bg-blue-100 text-blue-800" />
            </div>
          </div>
        </template>

        <template #title>
          <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
            {{ product.product_name }}
          </h3>
        </template>

        <template #content>
          <p class="text-gray-600 text-sm mb-4 line-clamp-3">
            {{ product.description }}
          </p>

          <!-- Variants -->
          <div class="mb-4" v-if="product.variants && product.variants.length > 0">
            <h4 class="text-sm font-medium text-gray-700 mb-2">Variants:</h4>
            <div class="space-y-2">
              <div v-for="variant in product.variants.slice(0, 2)" :key="variant.id"
                class="flex justify-between items-center text-sm">
                <span class="text-gray-600">{{ variant.product_variant_name }}</span>
                <div class="text-right">
                  <div class="font-semibold text-green-600">
                    {{ formatCurrency(variant.price) }}
                  </div>
                  <div class="text-xs text-gray-500">Stock: {{ variant.stock }}</div>
                </div>
              </div>
              <div v-if="product.variants.length > 2" class="text-xs text-blue-600">
                +{{ product.variants.length - 2 }} more variants
              </div>
            </div>
          </div>

          <!-- Reviews Summary -->
          <div v-if="product.reviews && product.reviews.length > 0" class="mb-4">
            <div class="flex items-center gap-2">
              <Rating :modelValue="getAverageRating(product.reviews)" :readonly="true" :stars="5" />
              <span class="text-sm text-gray-600">
                ({{ product.reviews.length }} reviews)
              </span>
            </div>
          </div>
        </template>

        <template #footer>
          <div class="flex gap-2">
            <Button label="View Details" icon="pi pi-eye" outlined size="small" class="flex-1"
              @click="viewProductDetails(product)" />
            <Button label="Add to Cart" icon="pi pi-shopping-cart" size="small" class="flex-1"
              @click="addToCart(product)" />
          </div>
        </template>
      </Card>
    </div>

    <!-- Empty State -->
    <Card v-else-if="!loading" class="text-center py-8">
      <template #content>
        <div class="text-gray-500">
          <i class="pi pi-search text-4xl mb-4"></i>
          <h3 class="text-lg font-semibold mb-2">No Products Found</h3>
          <p>Try adjusting your search criteria or filters</p>
        </div>
      </template>
    </Card>

    <!-- Pagination -->
    <div v-if="pagination.total > 0" class="flex justify-center">
      <Paginator :first="(pagination.current_page - 1) * pagination.per_page" :rows="pagination.per_page"
        :totalRecords="pagination.total" :rowsPerPageOptions="[10, 20, 50, 100]" @page="onPageChange" />
    </div>

    <!-- Product Detail Dialog -->
    <Dialog v-model:visible="showProductDetail" :header="selectedProduct?.product_name" modal :style="{ width: '50vw' }"
      :breakpoints="{ '960px': '75vw', '641px': '90vw' }">
      <div v-if="selectedProduct">
        <div class="mb-4">
          <img :src="selectedProduct.main_image || 'https://picsum.photos/500/300?random=' + selectedProduct.id"
            :alt="selectedProduct.product_name" class="w-full h-64 object-cover rounded" />
        </div>

        <div class="mb-4">
          <Tag :value="selectedProduct.category?.category_name" class="mb-2" />
          <p class="text-gray-600">{{ selectedProduct.description }}</p>
        </div>

        <!-- All Variants -->
        <div class="mb-4" v-if="selectedProduct.variants && selectedProduct.variants.length > 0">
          <h4 class="font-semibold mb-3">All Variants:</h4>
          <div class="space-y-3">
            <div v-for="variant in selectedProduct.variants" :key="variant.id"
              class="flex justify-between items-center p-3 border rounded">
              <div>
                <div class="font-medium">{{ variant.product_variant_name }}</div>
                <div class="text-sm text-gray-500">SKU: {{ variant.sku }}</div>
              </div>
              <div class="text-right">
                <div class="font-semibold text-green-600">{{ formatCurrency(variant.price) }}</div>
                <div class="text-sm text-gray-500">Stock: {{ variant.stock }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- All Reviews -->
        <div v-if="selectedProduct.reviews && selectedProduct.reviews.length > 0">
          <h4 class="font-semibold mb-3">Customer Reviews:</h4>
          <div class="space-y-3 max-h-64 overflow-y-auto">
            <div v-for="review in selectedProduct.reviews" :key="review.id" class="p-3 border rounded">
              <div class="flex justify-between items-start mb-2">
                <span class="font-medium">{{ review.user?.user_name }}</span>
                <Rating :modelValue="review.rating" :readonly="true" :stars="5" />
              </div>
              <p class="text-gray-600 text-sm">{{ review.comment }}</p>
            </div>
          </div>
        </div>
      </div>
    </Dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import apiClient from '@/api/axios'
import { useGlobalToast } from '@/composables/useGlobalToast'

// Reactive data
const products = ref([])
const loading = ref(false)
const showProductDetail = ref(false)
const selectedProduct = ref(null)
const categoryOptions = ref([])

const filters = reactive({
  search: '',
  category: null,
  min_price: null,
  max_price: null,
  sort: 'name_asc',
  page: 1,
  limit: 20
})

const pagination = reactive({
  current_page: 1,
  per_page: 20,
  total: 0,
  last_page: 1
})

const sortOptions = [
  { label: 'Name A-Z', value: 'name_asc' },     
  { label: 'Name Z-A', value: 'name_desc' },    
  { label: 'Price Low to High', value: 'price_asc' },
  { label: 'Price High to Low', value: 'price_desc' }
]

const { showSuccess, showError } = useGlobalToast()

// Debounce for search
let searchTimeout = null
const debounceSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    filters.page = 1
    fetchProducts()
  }, 500)
}

// Fetch categories dan products 
const fetchProducts = async () => {
  loading.value = true

  try {
    // Build query params, skip null/empty values
    const params = new URLSearchParams()

    if (filters.search && filters.search.trim()) params.append('search', filters.search.trim())
    if (filters.category) params.append('category', filters.category)
    if (filters.min_price !== null && filters.min_price !== undefined && filters.min_price !== '') {
      params.append('min_price', filters.min_price)
    }
    if (filters.max_price !== null && filters.max_price !== undefined && filters.max_price !== '') {
      params.append('max_price', filters.max_price)
    }
    if (filters.sort) params.append('sort', filters.sort)
    params.append('page', filters.page)
    params.append('limit', filters.limit)

    console.log('Fetching products with params:', params.toString()) // Debug log

    const response = await apiClient.get(`/products?${params.toString()}`)

    console.log('API Response:', response.data) // Debug log

    products.value = response.data.data || []

    // Update pagination - check if meta exists
    if (response.data.meta) {
      pagination.current_page = response.data.meta.current_page || 1
      pagination.per_page = response.data.meta.per_page || 20
      pagination.total = response.data.meta.total || 0
      pagination.last_page = response.data.meta.last_page || 1
    }

  } catch (error) {
    console.error('Error fetching products:', error)
    console.error('Error response:', error.response?.data) // More detailed error logging
    showError('Error', 'Failed to fetch products: ' + (error.response?.data?.message || error.message))
  } finally {
    loading.value = false
  }
}
const fetchCategories = async () => {
  try {
    const response = await apiClient.get('/categories')
    const categories = response.data.data || []

    // Mapping jadi format { label, value }
    categoryOptions.value = categories.map(cat => ({
      label: cat.category_name,
      value: cat.category_name
    }))

  } catch (error) {
    console.error('Error fetching categories:', error)
    showError('Error', 'Failed to load categories.')
  }
}


// Clear all filters
const clearFilters = () => {
  Object.assign(filters, {
    search: '',
    category: null,
    min_price: null,
    max_price: null,
    sort: 'name_asc',
    page: 1,
    limit: 20
  })
  fetchProducts()
}

// Handle pagination
const onPageChange = (event) => {
  filters.page = Math.floor(event.first / event.rows) + 1
  filters.limit = event.rows
  fetchProducts()
}

// Utility functions
const formatCurrency = (amount) => {
  if (!amount) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(parseFloat(amount))
}

const getAverageRating = (reviews) => {
  if (!reviews || reviews.length === 0) return 0
  const sum = reviews.reduce((acc, review) => acc + review.rating, 0)
  return sum / reviews.length
}

// Product actions
const viewProductDetails = (product) => {
  selectedProduct.value = product
  showProductDetail.value = true
}

const addToCart = (product) => {
  showSuccess('Success', `${product.product_name} added to cart!`)
}

// Initialize
onMounted(() => {
  fetchCategories(),
    fetchProducts()
})
</script>