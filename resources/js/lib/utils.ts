import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function valueUpdater<T>(updaterOrValue: ((current: T) => T) | T, value: T): T {
    if (Array.isArray(value)) {
        return (typeof updaterOrValue === 'function' ? (updaterOrValue as (current: T) => T)(value) : updaterOrValue) as T;
    }

    return typeof updaterOrValue === 'function' ? (updaterOrValue as (current: T) => T)(value) : updaterOrValue;
}
