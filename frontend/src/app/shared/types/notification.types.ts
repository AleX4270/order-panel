export interface Notification {
    id: string;
    title: string;
    message: string;
    readAt: string | null;
    createdAt: string;
    type: string;
}

export interface NotificationFilterParams {
    userId: number;
    onlyUnread: boolean;
}