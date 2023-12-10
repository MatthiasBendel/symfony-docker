# ----------------------------------------------------------------------------------------------------------------------
# --------------------------------------------------------
# ----------------------------------
# IMPORT STATEMENTS

from binance.client import Client
import time
# websocket
import websocket

import json

global THE_WEBSOCKET

global LIST_WITH_ALL_MESSAGES

global COUNTER


# ----------------------------------------------------------------------------------------------------------------------
# --------------------------------------------------------
# ----------------------------------
# START MAIN FUNCTION
def mainFunction():
    # start time
    # start_time = time.time()
    global THE_WEBSOCKET
    global LIST_WITH_ALL_MESSAGES
    LIST_WITH_ALL_MESSAGES = []
    global COUNTER
    COUNTER = 0

    socket = f'wss://stream.binance.com:9443/ws/!miniTicker@arr'
    THE_WEBSOCKET = websocket.WebSocketApp(
        socket,
        on_open=websocketOnOpen,
        on_message=websocketOnMessage,
        on_close=websocketOnClose,
        on_error=websocketOnError
    )
    THE_WEBSOCKET.run_forever()

    # print("--- %s seconds ---" % (time.time() - start_time))


# ----------------------------------------------------------------------------------------------------------------------
# --------------------------------------------------------
# ----------------------------------
# WEBSOCKET STUFF
def websocketOnMessage(ws, message):
    # print(message)

    global COUNTER

    global LIST_WITH_ALL_MESSAGES
    msg_as_obj = json.loads(message)
    LIST_WITH_ALL_MESSAGES.append(msg_as_obj)

    COUNTER += 1
    print(COUNTER)

    counter_reached = COUNTER % 1800
    if counter_reached == 0:
        with open('data_file_' + str(COUNTER) + '.txt', 'w') as file:
            file.write(json.dumps(LIST_WITH_ALL_MESSAGES))  # use `json.loads` to do the reverse
            print('file created')


def websocketOnError(ws, error):
    time.sleep(5)
    print("error occurred, websocket restarted")
    print(error)
    global THE_WEBSOCKET
    socket = f'wss://stream.binance.com:9443/ws/!miniTicker@arr'
    THE_WEBSOCKET = websocket.WebSocketApp(
        socket,
        on_open=websocketOnOpen,
        on_message=websocketOnMessage,
        on_close=websocketOnClose,
        on_error=websocketOnError
    )
    THE_WEBSOCKET.run_forever()


def websocketOnClose(ws, close_status_code, close_msg):
    print("### closed ###")
    print(close_status_code)
    print(close_msg)
    # restart websocket
    global THE_WEBSOCKET
    socket = f'wss://stream.binance.com:9443/ws/!miniTicker@arr'
    THE_WEBSOCKET = websocket.WebSocketApp(
        socket,
        on_open=websocketOnOpen,
        on_message=websocketOnMessage,
        on_close=websocketOnClose,
        on_error=websocketOnError
    )
    THE_WEBSOCKET.run_forever()


def websocketOnOpen(ws):
    print("websocket opened")


# ----------------------------------------------------------------------------------------------------------------------
# --------------------------------------------------------
# ----------------------------------
# START MAIN FUNCTION
if __name__ == '__main__':
    mainFunction()
