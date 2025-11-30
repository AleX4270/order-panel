import { Component, computed, input, InputSignal, Signal } from '@angular/core';
import { AbstractControl } from '@angular/forms';
import { ErrorLabelPipe } from '../../pipes/error-label.pipe';

@Component({
    selector: 'app-input-error-label',
    imports: [ErrorLabelPipe],
    template: `
        @if(control() && control()?.invalid && (control()?.dirty || control()?.touched)) {
            <div class="ps-2">
                @for(error of errors(); track error) {
                    <span class="error-label">{{error | errorLabel: control()}}</span>
                }
            </div>    
        }
    `,
    styles: [`
        .error-label {
            color: var(--error-label-text-color);
            font-size: var(--font-size-xxs);
        }
    `],
})
export class InputErrorLabelComponent {
    public control: InputSignal<AbstractControl | null> = input.required<AbstractControl | null>();
    
    protected errors: Signal<string[] | null | undefined> = computed(() => {
        const value = this.control()?.errors;
        return value ? Object.keys(value) : value;
    });
}
