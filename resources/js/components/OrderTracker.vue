<template>
  <div class="tracker-wrapper">
    <div class="tracker-card">
      <div class="brand-header">
        <span class="brand-badge">LOGISTICS & SUPPLY</span>
        <h1>HALCON</h1>
      </div>

      <div class="form-section">
        <h2>Track Your Order</h2>
        <p class="instruction">
          Enter your Customer Number and Invoice Number below to check the real-time status of your shipment.
        </p>

        <form @submit.prevent="checkOrderStatus">
          <div class="input-group">
            <label for="customerNumber">Customer Number</label>
            <input id="customerNumber" v-model.trim="customerNumber" type="text" placeholder="e.g., 1" required />
          </div>

          <div class="input-group">
            <label for="invoiceNumber">Invoice Number</label>
            <input id="invoiceNumber" v-model.trim="invoiceNumber" type="text" placeholder="e.g., INV-2026-001"
              required />
          </div>

          <button type="submit" class="btn-submit" :disabled="loading">
            {{ loading ? 'Searching...' : 'Check Order Status' }}
          </button>
        </form>
      </div>

      <!-- Mensaje de error si no encuentra la orden -->
      <div v-if="errorMessage" class="alert-error">
        {{ errorMessage }}
      </div>

      <!-- Resultado dinámico de la orden desde MySQL -->
      <div v-if="orderResult" class="result-card">
        <div class="result-header">
          <h3>Order Details</h3>
          <span :class="['status-pill', statusClass]">
            {{ orderResult.status }}
          </span>
        </div>

        <div class="result-grid">
          <div class="result-item">
            <span class="label">Customer Name:</span>
            <span class="value">{{ orderResult.customerName }}</span>
          </div>
          <div class="result-item">
            <span class="label">Customer Number:</span>
            <span class="value">{{ orderResult.customerNumber }}</span>
          </div>
          <div class="result-item">
            <span class="label">Invoice Number:</span>
            <span class="value">{{ orderResult.invoiceNumber }}</span>
          </div>
          <div class="result-item">
            <span class="label">Order Date:</span>
            <span class="value">{{ orderResult.date }}</span>
          </div>
          <div class="result-item full-width">
            <span class="label">Delivery Address:</span>
            <span class="value">{{ orderResult.address }}</span>
          </div>
          <div class="result-item full-width" v-if="orderResult.notes">
            <span class="label">Tracking Update:</span>
            <span class="value">{{ orderResult.notes }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

const customerNumber = ref('');
const invoiceNumber = ref('');
const orderResult = ref(null);
const errorMessage = ref('');
const loading = ref(false);

const checkOrderStatus = async () => {
  errorMessage.value = '';
  orderResult.value = null;
  loading.value = true;

  try {
    const response = await axios.get('/api/track-order', {
      params: {
        customer_number: customerNumber.value,
        invoice_number: invoiceNumber.value
      }
    });
    orderResult.value = response.data;
  } catch (error) {
    errorMessage.value =
      'No order found matching that Customer Number and Invoice Number. Please verify your details and try again.';
  } finally {
    loading.value = false;
  }
};

const statusClass = computed(() => {
  if (!orderResult.value) return '';
  const status = orderResult.value.status.toLowerCase();
  if (status === 'delivered' || status === 'completed') return 'status-delivered';
  if (status === 'in route') return 'status-route';
  return 'status-process';
});
</script>

<style scoped>
.tracker-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
  padding: 20px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.tracker-card {
  background: #ffffff;
  width: 100%;
  max-width: 540px;
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.brand-header {
  background: #111827;
  color: #ffffff;
  padding: 24px 30px;
  border-bottom: 4px solid #f59e0b;
}

.brand-badge {
  font-size: 0.72rem;
  letter-spacing: 2px;
  color: #f59e0b;
  font-weight: 700;
}

.brand-header h1 {
  margin: 4px 0 0;
  font-size: 2rem;
  letter-spacing: 3px;
  font-weight: 800;
}

.form-section {
  padding: 28px 30px;
}

.form-section h2 {
  margin: 0 0 8px;
  color: #1e293b;
  font-size: 1.4rem;
}

.instruction {
  margin: 0 0 22px;
  color: #64748b;
  font-size: 0.92rem;
  line-height: 1.5;
}

.input-group {
  margin-bottom: 16px;
}

.input-group label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  color: #334155;
  margin-bottom: 6px;
}

.input-group input {
  width: 100%;
  padding: 11px 14px;
  border: 1.5px solid #cbd5e1;
  border-radius: 8px;
  font-size: 0.95rem;
  transition: border-color 0.2s;
  box-sizing: border-box;
}

.input-group input:focus {
  outline: none;
  border-color: #f59e0b;
}

.btn-submit {
  width: 100%;
  padding: 13px;
  background: #f59e0b;
  color: #111827;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  margin-top: 8px;
  transition: background 0.2s;
}

.btn-submit:hover {
  background: #d97706;
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.alert-error {
  margin: 0 30px 25px;
  padding: 12px 16px;
  background: #fef2f2;
  border-left: 4px solid #ef4444;
  color: #b91c1c;
  font-size: 0.88rem;
  border-radius: 6px;
}

.result-card {
  margin: 0 30px 30px;
  padding: 20px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
}

.result-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 12px;
  margin-bottom: 14px;
}

.result-header h3 {
  margin: 0;
  font-size: 1.05rem;
  color: #0f172a;
}

.status-pill {
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
}

.status-delivered {
  background: #dcfce7;
  color: #166534;
}

.status-route {
  background: #fef3c7;
  color: #92400e;
}

.status-process {
  background: #dbeafe;
  color: #1e40af;
}

.result-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.result-item {
  display: flex;
  flex-direction: column;
}

.result-item.full-width {
  grid-column: span 2;
}

.result-item .label {
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 600;
}

.result-item .value {
  font-size: 0.9rem;
  color: #1e293b;
  font-weight: 500;
  margin-top: 2px;
}
</style>
