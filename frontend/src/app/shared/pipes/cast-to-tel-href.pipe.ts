import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'castToTelHref',
})
export class CastToTelHrefPipe implements PipeTransform {
    transform(value: string | null | undefined): string | null {
        if(value === null || value === undefined) {
            return null;
        }

        return 'tel:+48' + value.replace(/[\s()-]/g, '');
    }
}
