import { inject, Injectable } from '@angular/core';
import { ChannelType } from '../../types/broadcast.types';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { environment } from '../../../../environments/environment';
import { firstValueFrom } from 'rxjs';
import { HttpClient } from '@angular/common/http';

(window as any).Pusher = Pusher;

@Injectable({
  providedIn: 'root',
})
export class BroadcastService {
    private readonly http = inject(HttpClient);
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
            forceTLS: environment.echoConfig.forceTls,
            enabledTransports: ['ws', 'wss'],
            authorizer: (channel, options) => {
                return {
                    authorize: (socketId, callback: Function) => {
                        firstValueFrom(this.http.post(environment.echoConfig.authEndpoint, {
                            socket_id: socketId,
                            channel_name: channel.name
                        }, {
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                        }))
                        .then(response => {
                            callback(false, response);
                        })
                        .catch(error => {
                            callback(true, error);
                        })
                    }
                };
            },
        });
    }

    public listenToChannel<T>(name: string, type: ChannelType, handler: (eventName: string, data: T) => void) {
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
