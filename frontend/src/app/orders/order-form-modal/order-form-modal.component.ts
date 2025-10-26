import { Component, ElementRef, OnDestroy, ViewChild } from '@angular/core';

@Component({
    selector: 'app-order-form-modal',
    imports: [],
    template: `
        <div #modalRef class="modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Modal body text goes here.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    `,
    styles: [``],
})
export class OrderFormModalComponent implements OnDestroy {
    @ViewChild('modalRef') orderFormModal!: ElementRef;

    private modal?: any;

    public openModal(): void {
        if(!this.modal) {
            this.modal = new window.bootstrap.Modal(this.orderFormModal.nativeElement);
        }

        if(typeof window !== 'undefined' && window.bootstrap) {
            this.modal.show();
        }
    }

    protected closeModal(): void {
        this.modal?.hide();
    }

    ngOnDestroy(): void {
        this.modal?.dispose();
    }
}
