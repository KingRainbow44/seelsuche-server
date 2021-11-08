<?php

namespace seelsuche\server\network;

interface InboundPacketIds
{
    const CLIENT_PING_RESPONSE = '0x01';
    const CLIENT_AUTH = '0x02';
    const CHAT_RECEIVE_PACKET = '0x03';
}