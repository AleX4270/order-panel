export enum Permission {
    //Orders
    orders_view = 'orders.view',
    orders_show = 'orders.show',
    orders_create = 'orders.create',
    orders_update = 'orders.update',
    orders_delete = 'orders.delete',
    orders_mark_as_completed = 'orders.mark_as_completed',

    //Order Requests
    order_requests_view = 'order_requests.view',
    order_requests_show = 'order_requests.show',
    order_requests_manage = 'order_requests.manage',

    //Users
    users_view = 'users.view',
    users_show = 'users.show',
    users_create = 'users.create',
    users_update = 'users.update',
    users_delete = 'users.delete',

    //Dashboard
    dashboard_view = 'dashboard.view',

    //Company
    company_manage = 'company.manage',
}