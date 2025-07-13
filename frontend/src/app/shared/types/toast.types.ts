import { ToastType } from "../enums/enums";

export interface Toast {
    id: number;
    message: string;
    type: ToastType;
    duration?: number;
}