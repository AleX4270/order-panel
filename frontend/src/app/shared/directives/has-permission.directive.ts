import { Directive, inject, Input, TemplateRef, ViewContainerRef } from '@angular/core';
import { UserPermissionService } from '../services/user-permission/user-permission.service';

type PermissionMode = 'every' | 'any';

@Directive({
    selector: '[hasPermission]'
})
export class HasPermissionDirective {
    private readonly userPermissionService = inject(UserPermissionService);
    private readonly templateRef: TemplateRef<any> = inject(TemplateRef<any>);
    private readonly viewContainer: ViewContainerRef = inject(ViewContainerRef);

    private requiredPermission: string | string[] = '';
    private requiredPermissionMode: PermissionMode = 'every';

    @Input('hasPermission') set permission(value: string | string[]) {
        this.requiredPermission = value;
        this.checkPermission();
    }

    @Input('requiredPermissionMode') set permissionMode(value: PermissionMode | undefined) {
        this.requiredPermissionMode = value ?? 'every';
        this.checkPermission();
    }

    private checkPermission(): void {
        const hasPermission = this.requiredPermissionMode === 'every' 
            ? this.userPermissionService.hasEveryPermission(this.requiredPermission)
            : this.userPermissionService.hasAnyPermissions(this.requiredPermission);

        this.viewContainer.clear();
        if (hasPermission) {
            this.viewContainer.createEmbeddedView(this.templateRef);
        }
    }
}
