import { Component, effect, ElementRef, inject, ViewChild } from '@angular/core';
import { TranslatePipe } from '@ngx-translate/core';
import { PromptModalService } from '../../services/prompt-modal/prompt-modal.service';
import { PromptModalConfig } from '../../types/prompt-modal.types';

@Component({
    selector: 'app-prompt-modal',
    imports: [
        TranslatePipe,
    ],
    template: `
        <dialog #modalRef class="modal">
            <div class="modal-box">
                <div class="modal-header">
                    <h5 class="text-error text-lg">{{ config?.title }}</h5>
                </div>

                <div class="divider p-0 m-0"></div>

                <div class="modal-content mt-2">
                    <span class="text-sm">{{ config?.message }}</span>
                </div>

                <div class="modal-actions w-full mt-10">
                    <form method="dialog" class="flex justify-end gap-2">
                        <button class="btn btn-sm btn-outline btn-error">{{"basic.cancel" | translate}}</button>
                        <button type="button" class="btn btn-primary btn-sm" (click)="handleAccept()">{{"basic.confirm" | translate}}</button>
                    </form>
                </div>
            </div>
        </dialog>
    `,
    styles: [``]
})
export class PromptModalComponent {
    @ViewChild('modalRef') promptModal!: ElementRef;

    private readonly promptModalService: PromptModalService = inject(PromptModalService);
    protected config: PromptModalConfig | null = null;

    constructor() {
        effect(() => {
            const config = this.promptModalService.activeModal();

            if(config) {
                this.config = config;
                this.openModal();
            }
            else {
                this.config = null;
            }
        })
    }

    protected openModal(): void {
        const modal = this.promptModal.nativeElement as HTMLDialogElement;
        if(!modal) {
            return;
        }

        modal.showModal();
    }

    protected handleAccept(): void {
        this.config?.handler();
        this.closeModal();
    }

    protected closeModal(): void {
        const modal = this.promptModal.nativeElement as HTMLDialogElement;
        if(!modal) {
            return;
        }

        modal.close();
    }
}
