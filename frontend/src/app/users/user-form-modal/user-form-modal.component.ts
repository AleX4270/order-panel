import { DatePipe } from '@angular/common';
import { Component, DestroyRef, ElementRef, inject, output, signal, ViewChild, WritableSignal } from '@angular/core';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { InputErrorLabelComponent } from '../../shared/components/input-error-label/input-error-label.component';
import { TranslatePipe, TranslateService } from '@ngx-translate/core';
import { ToastService } from '../../shared/services/toast/toast.service';
import { ToastType } from '../../shared/enums/enums';
import { UserItem, UserParams } from '../../shared/types/user.types';
import { UserService } from '../../shared/services/api/user/user.service';
import { validatePasswordStrength } from '../../shared/validators/password-strength.validator';
import { validatePasswordMatch } from '../../shared/validators/password-match.validator';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { NgSelectComponent } from '@ng-select/ng-select';
import { RoleItem } from '../../shared/types/role.types';
import { RoleService } from '../../shared/services/api/role/role.service';
import { ButtonComponent } from "../../shared/components/button/button.component";
import { finalize, forkJoin } from 'rxjs';
import { NotificationChannelService } from '../../shared/services/api/notification-channel/notification-channel.service';
import { NotificationEventService } from '../../shared/services/api/notification-event/notification-event.service';
import { NotificationChannelItem } from '../../shared/types/notification-channel.types';
import { NotificationEventItem } from '../../shared/types/notification-event.types';
import { NotificationSettingsSet } from '../../shared/types/notification.types';

@Component({
    selector: 'app-user-form-modal',
    imports: [ReactiveFormsModule, InputErrorLabelComponent, TranslatePipe, NgSelectComponent, ButtonComponent],
    providers: [DatePipe],
    template: `
        <dialog #modalRef class="modal">
            <div class="modal-box max-w-3xl">
                <div class="header">
                    <h1 class="text-primary font-semibold text-xl">{{ ("userForm." + (isEditScenario() ? "updateTitle" : "createTitle") | translate) + (isEditScenario() ? ' - #' + this.userId() : '') }}</h1>
                    <form method="dialog">
                        <app-button type="submit" classList="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</app-button>
                    </form>
                </div>

                <div class="content [&_label]:text-xs [&_label]:font-light [&_label]:mb-1">
                    @if(!isLoading() && form) {
                        <form [formGroup]="form" class="w-full">
                            <div class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap mt-4">
                                <div class="w-full flex flex-col flex-1">
                                    <label for="firstName">{{ "userForm.firstName" | translate }}</label>
                                    <input
                                        type="text"
                                        formControlName="firstName"
                                        id="firstName"
                                        name="firstName"
                                        class="input text-xs w-full"
                                        [placeholder]="'userForm.firstNamePlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('firstName')" />
                                </div>

                                <div class="w-full flex flex-col flex-1">
                                    <label for="lastName">{{ "userForm.lastName" | translate }}</label>
                                    <input
                                        type="text"
                                        formControlName="lastName"
                                        id="lastName"
                                        name="lastName"
                                        class="input text-xs w-full"
                                        [placeholder]="'userForm.lastNamePlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('lastName')" />
                                </div>
                            </div>

                            <div class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap mt-4">
                                <div class="w-full flex flex-col flex-1">
                                    <label for="username" class="field-required">{{ "userForm.username" | translate }}</label>
                                    <input
                                        type="text"
                                        formControlName="username"
                                        id="username"
                                        name="username"
                                        class="input text-xs w-full"
                                        [placeholder]="'userForm.usernamePlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('username')" />
                                </div>

                                <div class="w-full flex flex-col flex-1">
                                    <label for="email" class="field-required">{{ "userForm.email" | translate }}</label>
                                    <input
                                        type="email"
                                        formControlName="email"
                                        id="email"
                                        name="email"
                                        class="input text-xs w-full"
                                        [placeholder]="'userForm.emailPlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('email')" />
                                </div>
                            </div>

                            <div class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap mt-4">
                                <div class="w-full flex flex-col flex-1">
                                    <label for="roles">{{ "userForm.roles" | translate }}</label>
                                    <ng-select
                                        formControlName="roles"
                                        [items]="roles()"
                                        bindValue="symbol"
                                        bindLabel="name"
                                        [multiple]="true"
                                        [placeholder]="'userForm.roles' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('roles')" />
                                </div>
                            </div>

                            <div class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap mt-4">
                                <div class="w-full flex flex-col flex-1">
                                    <label for="password">{{ "userForm.password" | translate }}</label>
                                    <input
                                        type="password"
                                        formControlName="password"
                                        id="password"
                                        name="password"
                                        class="input text-xs w-full"
                                        [placeholder]="'userForm.passwordPlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('password')" />
                                </div>

                                <div class="w-full flex flex-col flex-1">
                                    <label for="passwordConfirmed">{{ "userForm.passwordConfirmed" | translate }}</label>
                                    <input
                                        type="password"
                                        formControlName="passwordConfirmed"
                                        id="passwordConfirmed"
                                        name="passwordConfirmed"
                                        class="input text-xs w-full"
                                        [placeholder]="'userForm.passwordConfirmedPlaceholder' | translate"
                                    />
                                    <app-input-error-label [control]="form.get('passwordConfirmed')" />
                                </div>
                            </div>

                            <div class="divider"></div>

                            <div class="w-full flex flex-col items-center gap-y-3 md:flex-row md:gap-3 md:flex-wrap mt-4">
                                <h2 class="text-primary font-semibold text-md">{{ "userForm.notificationsSettings" | translate }}</h2>
                                @for(event of notificationEvents(); track event.id) {
                                    <div class="w-full flex items-center">
                                        <h3 class="text-xs font-semibold me-3">{{event.name}}</h3>
                                        <div class="w-full flex items-center gap-4">
                                            @for(channel of notificationChannels(); track channel.id) {
                                                <div>
                                                    <input
                                                        type="checkbox"
                                                        [id]="'event-' + event.id + '-channel-' + channel.id"
                                                        [name]="'event-' + event.id + '-channel-' + channel.id"
                                                        class="toggle toggle-sm me-1"
                                                        (change)="switchNotificationEventChannel($event.target.checked, event.id, channel.id)"
                                                        [checked]="isChannelChecked(event.id, channel.id)"
                                                    /> 
                                                    <label [for]="'event-' + event.id + '-channel-' + channel.id" class="cursor-pointer">{{ channel.name }}</label>
                                                </div>
                                            }
                                        </div>
                                    </div>
                                }
                            </div>
                        </form>
                    }
                </div>

                <div class="footer">
                    <div class="modal-action w-full">
                        <form method="dialog" class="flex items-center gap-3">
                            <app-button type="submit" classList="btn btn-outline btn-sm btn-error">{{"basic.cancel" | translate}}</app-button>
                            <app-button type="button" classList="btn btn-primary btn-sm" (click)="saveUser()" [isLoading]="isSubmitted()" [isDisabled]="isSubmitted()">{{"basic.save" | translate}}</app-button>
                        </form>
                    </div>
                </div>
            </div>
        </dialog>
    `,
    styles: [``],
})
export class UserFormModalComponent {
    @ViewChild('modalRef') userFormModal!: ElementRef;

    private readonly translateService: TranslateService = inject(TranslateService);
    private readonly toastService: ToastService = inject(ToastService);
    private readonly formBuilder: FormBuilder = inject(FormBuilder);
    private readonly userService: UserService = inject(UserService);
    private readonly destroyRef: DestroyRef = inject(DestroyRef);
    private readonly roleService: RoleService = inject(RoleService);
    private readonly notificationChannelService: NotificationChannelService = inject(NotificationChannelService);
    private readonly notificationEventService: NotificationEventService = inject(NotificationEventService);

    protected form!: FormGroup;
    protected userId: WritableSignal<number | null> = signal<number | null>(null);
    protected isEditScenario: WritableSignal<boolean> = signal<boolean>(false);
    protected isLoading: WritableSignal<boolean> = signal<boolean>(false);
    protected roles: WritableSignal<RoleItem[]> = signal<RoleItem[]>([]);
    protected isSubmitted: WritableSignal<boolean> = signal<boolean>(false);
    protected notificationChannels: WritableSignal<NotificationChannelItem[]> = signal<NotificationChannelItem[]>([]);
    protected notificationEvents: WritableSignal<NotificationEventItem[]> = signal<NotificationEventItem[]>([]);

    private notificationSettingsMap: NotificationSettingsSet = new Map<number, Set<number>>();

    protected userSaved = output<void>();

    public showForm(userId?: number): void {
        this.isLoading.set(true);
        this.cleanFormMetaData();
        this.initForm();

        if(userId) {
            this.userId.set(userId);
            this.isEditScenario.set(true);
            this.loadDetails(userId);
        }

        forkJoin({
            roles: this.roleService.index(),
            notificationChannels: this.notificationChannelService.index(),
            notificationEvents: this.notificationEventService.index(),
        }).subscribe({
            next: (res) => {
                this.roles.set(res.roles.data?.items ?? []);
                this.notificationChannels.set(res.notificationChannels.data ?? []);
                this.notificationEvents.set(res.notificationEvents.data ?? []);

                this.isLoading.set(false);

                // TODO: Remove debug
                console.log(this.roles());
                console.log(this.notificationChannels());
                console.log(this.notificationEvents());
            },
        })
        
        this.openModal();     
    }
    
    protected openModal(): void {
        const modal = this.userFormModal.nativeElement as HTMLDialogElement;
        if(!modal) {
            return;
        }

        modal.showModal();
    }

    protected closeModal(): void {
        const modal = this.userFormModal.nativeElement as HTMLDialogElement;
        if(!modal) {
            return;
        }

        modal.close();
        this.isEditScenario.set(false);
        this.userId.set(null);
        this.form.reset();
    }

    protected cleanFormMetaData(): void {
        this.userId.set(null);
        this.isEditScenario.set(false);
        this.notificationSettingsMap.clear();
    }

    private initForm(): void {
        this.form = this.formBuilder.group({
            id: [null],
            firstName: [null],
            lastName: [null],
            username: [null, Validators.required],
            email: [null, [Validators.required, Validators.pattern(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)]],
            password: [null, [validatePasswordStrength()]],
            passwordConfirmed: [null, [validatePasswordMatch()]],
            roles: [null],
        });

        this.registerFormChanges();
    }

    private registerFormChanges(): void {
        const password = this.form.get('password');

        if(!password) {
            return;
        }

        password.valueChanges.pipe(
            takeUntilDestroyed(this.destroyRef),
        )
        .subscribe({
            next: () => {
                this.form.get('passwordConfirmed')?.updateValueAndValidity();
            }
        });
    }

    private loadDetails(userId: number): void {
        this.userService.show(userId).subscribe({
            next: (res) => {
                const user: UserItem | null = res.data;
                if(!user) {
                    return;
                }

                if(user.isInternal) {
                    this.form.get('roles')?.disable();
                }

                let roleSymbols: string[] = [];
                if(user.roles && user.roles.length > 0) {
                    roleSymbols = user.roles.map(role => role.symbol);
                }

                if(user.notificationSettings && user.notificationSettings.length > 0) {
                    user.notificationSettings.map((setting) => {
                        this.notificationSettingsMap.set(setting.eventId, new Set(setting.channelIds));
                    })
                }

                this.form.patchValue({
                    id: user.id,
                    firstName: user.firstName,
                    lastName: user.lastName,
                    username: user.name,
                    email: user.email,
                    roles: roleSymbols ?? null,
                });
            },
            error: (err) => {
                console.error(err);
            }
        });
    }

    protected saveUser(): void {
        if(this.isSubmitted()) {
            return;
        }

        if(this.form.invalid) {
            this.form.markAllAsDirty();
            this.toastService.show(this.translateService.instant('form.error'), ToastType.danger);
            return;
        }

        this.isSubmitted.set(true);
        const formValues = this.form.value;

        // TODO: Absolutely improve this
        let userParams = {
            username: formValues.username,
            email: formValues.email,
        } as UserParams;

        if(formValues.id) {
            userParams.id = formValues.id;
        }

        if(formValues.firstName) {
            userParams.firstName = formValues.firstName;
        }

        if(formValues.lastName) {
            userParams.lastName = formValues.lastName;
        }

        if(formValues.password) {
            userParams.password = formValues.password;
        }

        if(formValues.passwordConfirmed) {
            userParams.passwordConfirmed = formValues.passwordConfirmed;
        }

        if(formValues.roles) {
            userParams.roles = formValues.roles;
        }

        userParams.notificationSettings = Array.from(this.notificationSettingsMap.entries()).map(([eventId, channelIds]) => ({
            eventId,
            channelIds: Array.from(channelIds)
        }));

        const method = this.isEditScenario()
            ? this.userService.update(userParams)
            : this.userService.store(userParams);

        method
        .pipe(
            finalize(() => this.isSubmitted.set(false))
        )
        .subscribe({
            next: (res) => {
                this.toastService.show(
                    this.translateService.instant('userForm.saveSuccessMessage'),
                    ToastType.success,
                );
                this.userSaved.emit();
            },
            error: (err) => {
                console.log(err);
                this.toastService.show(
                    this.translateService.instant('userForm.saveErrorMessage'),
                    ToastType.danger,
                );
            },
            complete: () => {
                this.closeModal();
            }
        });
    }

    protected switchNotificationEventChannel(isChecked: boolean, eventId: number, channelId: number): void {
        if(!this.notificationSettingsMap.has(eventId)) {
            this.notificationSettingsMap.set(eventId, new Set());
        }

        let channels = this.notificationSettingsMap.get(eventId);

        if(isChecked) {
            channels?.add(channelId);
        }
        else {
            channels?.delete(channelId);
        }
    }

    protected isChannelChecked(eventId: number, channelId: number): boolean {
        if(!this.notificationSettingsMap.has(eventId)) {
            return false;
        }

        const channels = this.notificationSettingsMap.get(eventId)!;
        return channels?.has(channelId);
    }
}
