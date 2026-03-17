import { Injectable } from '@angular/core';
import { ChannelType } from '../../types/broadcast.types';
import Echo from 'laravel-echo';
import { environment } from '../../../../environments/environment';

@Injectable({
  providedIn: 'root',
})
export class BroadcastService {
    private driver!: Echo<'reverb'>;

    constructor() {
        this.initializeDriver();
    }

    private initializeDriver() {
        this.driver = new Echo({
            broadcaster: 'reverb',
            key: environment.echoConfig.key,
            wsHost: environment.echoConfig.wsHost,
            wsPort: environment.echoConfig.wsPort,
            wssPort: environment.echoConfig.wssPort,
            forceTls: environment.echoConfig.forceTls,
            enabledTransports: ['ws', 'wss'],
            //todo: custom ? authorizer: 
        });
    }

    public listenToChannel<T>(name: string, type: ChannelType, handler: (data: T) => void) {
        if(type === ChannelType.public) {
            this.driver.channel(name)
                .listenToAll(handler);
        }
        else {
            this.driver.private(name)
                .listenToAll(handler);
        }
    }

    public leaveChannel(name: string) {
        this.driver.leaveChannel(name);
    }
}
