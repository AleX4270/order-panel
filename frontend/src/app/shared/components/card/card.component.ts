
import { Component, computed, input, signal } from '@angular/core';
import { NgIcon, provideIcons } from '@ng-icons/core';
import { faSolidChevronDown, faSolidChevronUp } from '@ng-icons/font-awesome/solid';

@Component({
    selector: 'app-card',
    imports: [NgIcon],
    providers: [provideIcons({faSolidChevronUp, faSolidChevronDown})],
    template: `
        <div 
            class="card card-border bg-base-100 w-full shadow-sm"
            [class.h-full]="isFullHeight() && !isCollapsed()"
        >
            <div class="card-body px-4 py-4">
                <div class="flex gap-2 items-center">
                    @if(isCollapsible()) {
                        <ng-icon
                            class="item-pressable"
                            [name]="toggleIcon()"
                            size="16px"
                            (click)="toggleCollapseState()"
                        ></ng-icon>
                    }
                    @if(title()) {
                        <h2 class="card-title text-sm text-base-content/70">{{ title() }}</h2>
                    }
                </div>
                <div
                    [class.flex]="isContentCentered()"
                    [class.items-center]="isContentCentered()"
                    [class.justify-center]="isContentCentered()"
                    [class.h-full]="isFullHeight() && !isCollapsed()"
                    [class.hidden]="isCollapsed()"
                >
                    <ng-content></ng-content>
                </div>
            </div>
        </div>
    `,
    styles: [``],
})
export class CardComponent {
    public isContentCentered = input<boolean>(false);
    public isFullHeight = input<boolean>(false);
    public isCollapsible = input<boolean>(false);
    public title = input<string | null>(null);

    public overflowType = input<'hidden' | 'visible' | 'scroll'>('hidden');
    public color = input<string | null>(null);

    protected isCollapsed = signal(false);

    protected toggleIcon = computed(() => {
        return this.isCollapsed() ? 'faSolidChevronDown' : 'faSolidChevronUp';
    });

    protected toggleCollapseState(): void {
        this.isCollapsed.set(!this.isCollapsed());
    }
}
