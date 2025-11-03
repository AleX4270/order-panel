import { inject, Pipe, PipeTransform } from '@angular/core';
import { AbstractControl } from '@angular/forms';
import { TranslateService } from '@ngx-translate/core';

@Pipe({
    name: 'errorLabel'
})
export class ErrorLabelPipe implements PipeTransform {
    private readonly translate: TranslateService = inject(TranslateService);

    transform(value: string, control: AbstractControl | null): string | null {
        if(!value) return null;

        switch (value) {
            case 'required': return this.translate.instant('error.required');
            case 'email': return this.translate.instant('error.email');
            case 'min': return this.translate.instant('error.min', { min: control?.errors?.['min'] });
            case 'max': return this.translate.instant('error.max', { max: control?.errors?.['max'] });
            case 'maxlength': return this.translate.instant('error.maxLength');
            case 'minlength': return this.translate.instant('error.minLength');
            case 'pattern': return this.translate.instant('error.pattern');
            default: return this.translate.instant('error.incorrect');
        }
    }
}
