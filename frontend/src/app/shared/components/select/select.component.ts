import { CommonModule } from '@angular/common';
import { Component, forwardRef, input, InputSignal, signal, WritableSignal } from '@angular/core';
import { ControlValueAccessor, NG_VALUE_ACCESSOR } from '@angular/forms';

@Component({
    selector: 'app-select',
    imports: [CommonModule],
    template: `
        <div class="w-full">
            <div class="dropdown">
                <div tabindex="0" role="button" class="select flex flex-wrap items-center min-h-9 h-auto gap-0" [ngClass]="{'pt-0 py-1': selectedItems() && selectedItems().length > 0}">
                    @if(selectedItems() && selectedItems().length > 0) {
                        <button type="button" class="btn btn-sm text-base-content/30 border-none bg-transparent hover:text-error cursor-pointer p-0 pe-2 m-0" (mousedown)="removeAllItems(); preventDropdownRendering($event)">x</button>

                        @for(selectedItem of selectedItems(); track selectedItem; let i = $index) {
                            <div class="badge badge-sm badge-soft badge-primary min-w-8 flex flex-row justify-start me-1 hover:none">
                                <button type="button" class="text-primary/40 hover:text-error cursor-pointer p-0 m-0" (mousedown)="onRemoveClick($event, i)">x</button>
                                <span>{{ selectedItem[bindLabel()] ?? selectedItem[bindValue()] ?? selectedItem }}</span>
                            </div>
                        }
                    }
                    @else {
                        <span>{{ placeholder() }}</span>
                    }
                </div>
                <ul tabindex="-1" class="dropdown-content menu bg-base-100 rounded-box z-1 w-52 p-2 shadow-sm">
                    @for(item of items(); track item) {
                        <li
                            [ngClass]="{'text-base-content/20': isItemSelected(item)}"
                        >
                            <button 
                                type="button" 
                                class="cursor-pointer" 
                                (click)="selectItem(item)"
                            >
                                {{ item[bindLabel()] ?? item[bindValue()] ?? item }}
                            </button>
                        </li>
                    }
                </ul>
            </div>
        </div>
    `,
    styles: [``],
    providers: [
        {
            provide: NG_VALUE_ACCESSOR,
            useExisting: forwardRef(() => SelectComponent),
            multi: true,
        }
    ],
})
export class SelectComponent implements ControlValueAccessor {
    public items: InputSignal<any[]> = input.required<any[]>();

    public placeholder: InputSignal<string> = input<string>('');
    public bindValue: InputSignal<string> = input<string>('id');
    public bindLabel: InputSignal<string> = input<string>('name');
    public closeOnSelect: InputSignal<boolean> = input<boolean>(true);
    
    protected selectedItems: WritableSignal<any[]> = signal<any[]>([]);
    protected isExpanded: WritableSignal<boolean> = signal<boolean>(false);
    
    writeValue(obj: any): void {
        
    }

    registerOnChange(fn: any): void {
        
    }

    registerOnTouched(fn: any): void {
        
    }

    setDisabledState(isDisabled: boolean): void {
        
    }

    selectItem(item: any): void {
        if(this.closeOnSelect()) {
            this.blurActive();
        }

        const existingItemIndex = this.selectedItems().findIndex((selectedItem) => {
            if(selectedItem[this.bindValue()] && item[this.bindValue()] && selectedItem[this.bindValue()] === item[this.bindValue()]) {
                return true;
            }
            else if(selectedItem[this.bindLabel()] && item[this.bindLabel()] && selectedItem[this.bindLabel()] === item[this.bindLabel()]) {
                return true;
            }
            else if(selectedItem === item) {
                return true;
            }

            return false;
        });

        if(existingItemIndex !== -1) {
            this.removeItem(existingItemIndex);
            return;
        }

        this.selectedItems.update((val) => [...val, item]);
    }

    private removeItem(index: number): void {
        const updatedSelectedItems = this.selectedItems();
        updatedSelectedItems.splice(index, 1);
        this.selectedItems.set(updatedSelectedItems);
    }

    protected removeAllItems(): void {
        this.selectedItems.set([]);
    }

    protected onRemoveClick(event: Event, index: number): void {
        this.preventDropdownRendering(event);
        this.removeItem(index);
    }

    protected isItemSelected(item: any): boolean {
        return this.selectedItems().includes(item);
    }

    private blurActive(): void {
        (document.activeElement as HTMLElement | null)?.blur();
    }

    protected preventDropdownRendering(event: Event): void {
        event.preventDefault();
        event.stopPropagation();
        (event.target as HTMLElement)?.blur();
    }
}
