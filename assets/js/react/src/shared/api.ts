import { config } from './config';

async function api<T>(path: string, init: RequestInit = {}): Promise<T> {
    const res = await fetch(config.restUrl + path, {
        ...init,
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': config.nonce,
            ...(init.headers || {}),
        },
    });
    if (!res.ok) {
        throw new Error(`API ${path} ${res.status}`);
    }
    return res.json();
}

/* ═══ 购物车 ═══ */
export interface CartItem {
    id: number;
    product_id: number;
    product_name: string;
    unit_price: number;
    quantity: number;
    subtotal: number;
}

export const getCartItems = () => api<{ items: CartItem[] }>('/cart/items');
export const addCartItem = (product_id: number, quantity: number) =>
    api('/cart/items', { method: 'POST', body: JSON.stringify({ product_id, quantity }) });
export const updateCartItem = (id: number, quantity: number) =>
    api(`/cart/items/${id}`, { method: 'PUT', body: JSON.stringify({ quantity }) });
export const removeCartItem = (id: number) =>
    api(`/cart/items/${id}`, { method: 'DELETE' });

/* ═══ 订单 ═══ */
export interface Order {
    id: number;
    order_number: string;
    status: string;
    total: number;
    created_at: string;
}

export const getOrders = () => api<{ data: Order[] }>('/orders');
export const getOrder = (id: number) => api<{ data: Order }>(`/orders/${id}`);

/* ═══ 结账 ═══ */
export interface CheckoutData {
    name: string;
    email: string;
    phone?: string;
    address: string;
    payment_method?: string;
}

export const createOrder = (data: CheckoutData) =>
    api<{ success: boolean; order_id?: number; error?: string }>('/checkout', {
        method: 'POST',
        body: JSON.stringify(data),
    });

/* ═══ 用户 ═══ */
export const updateProfile = (data: Record<string, unknown>) =>
    api('/user/profile', { method: 'POST', body: JSON.stringify(data) });