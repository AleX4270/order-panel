import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CompanyHeadquartersMapComponent } from './company-headquarters-map.component';

describe('CompanyHeadquartersMapComponent', () => {
  let component: CompanyHeadquartersMapComponent;
  let fixture: ComponentFixture<CompanyHeadquartersMapComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CompanyHeadquartersMapComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CompanyHeadquartersMapComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
