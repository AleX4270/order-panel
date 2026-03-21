import { TestBed } from '@angular/core/testing';

import { NotificationDrawerService } from './notification-drawer.service';

describe('NotificationDrawerService', () => {
  let service: NotificationDrawerService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(NotificationDrawerService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
