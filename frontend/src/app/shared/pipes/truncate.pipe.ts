import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
    name: 'truncate',
})
export class TruncatePipe implements PipeTransform {
    transform(value: string | null | undefined, limit: number = 50, ellipsis: string = '…'): string | null {
        if (value === null || value === undefined) {
            return null;
        }

        if (value.length <= limit) {
            return value;
        }

        return value.slice(0, limit) + ellipsis;
    }
}
