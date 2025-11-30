import { Injectable, signal } from '@angular/core';
import { PromptModalConfig } from '../../types/prompt-modal.types';

@Injectable({
    providedIn: 'root'
})
export class PromptModalService {
    public activeModal = signal<PromptModalConfig | null>(null);

    public openModal(config: PromptModalConfig): void {
        this.activeModal.set(config);
    }

    public closeModal(): void {
        this.activeModal.set(null);
    }
}
