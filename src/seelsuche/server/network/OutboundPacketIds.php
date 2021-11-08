<?php

namespace seelsuche\server\network;

interface OutboundPacketIds
{
    const CLIENT_PING = '0x01';
    const CLIENT_AUTH_RESPONSE = '0x02';
    const SAVE_DATA_RESPONSE = '0x03';
    const CHAT_DISPATCH_PACKET = '0x04';
}