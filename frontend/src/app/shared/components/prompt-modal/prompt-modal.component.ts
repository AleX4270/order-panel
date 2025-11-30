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
        <div #modalRef class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary">{{ config?.title }}</h5>
                    </div>
                    <div class="modal-body">
                        <small>{{ config?.message }}</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-danger" (click)="closeModal()">{{"basic.cancel" | translate}}</button>
                        <button type="button" class="btn btn-sm btn-primary" (click)="handleAccept()">{{"basic.confirm" | translate}}</button>
                    </div>
                </div>
            </div>
        </div>
    `,
    styles: [``]
})
export class PromptModalComponent {
    @ViewChild('modalRef') promptModal!: ElementRef;

    private readonly promptModalService: PromptModalService = inject(PromptModalService);
    private modal?: any;
    
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
        if(!this.modal) {
            this.modal = new window.bootstrap.Modal(this.promptModal.nativeElement, {
                focus: true,
                keyboard: false,
                backdrop: 'static'
            });
        }

        if(typeof window !== 'undefined' && window.bootstrap) {
            this.modal.show();
        }
    }

    protected handleAccept(): void {
        this.config?.handler();
        this.closeModal();
    }

    protected closeModal(): void {
        this.promptModalService.closeModal();
        this.modal?.hide();
    }

    ngOnDestroy(): void {
        this.modal?.dispose();
    }
}
