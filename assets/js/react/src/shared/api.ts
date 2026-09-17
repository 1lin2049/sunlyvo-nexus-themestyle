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
    if (!res.ok) throw new Error(`API ${path} ${res.status}`);
    return res.json();
}

export const getCart = () => api<{ cart: any }>('/cart');
export const getCartItems = () => api<{ items: any[] }>('/cart/items');
export const addCartItem = (product_id: number, quantity: number) =>
    api('/cart/items', { method: 'POST', body: JSON.stringify({ product_id, quantity }) });
export const updateCartItem = (id: number, quantity: number) =>
    api(`/cart/items/${id}`, { method: 'PUT', body: JSON.stringify({ quantity }) });
export const removeCartItem = (id: number) =>
    api(`/cart/items/${id}`, { method: 'DELETE' });

export const getOrders = () => api<{ data: any[] }>('/orders');
export const getAddresses = () => api<{ data: any[] }>('/addresses');
export const updateProfile = (data: any) =>
    api('/user/profile', { method: 'POST', body: JSON.stringify(data) });