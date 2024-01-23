import logging

import time


def buy_directly_no_sell(coin="EUR"):
    return {
        "buy": True,
        "sell": 'never',
        "coin": coin
    }


def best_strategy():
    buy_directly_no_sell()

def log_secondly_for_one_minute():
    # Set the end time for the script to run for 1 minute (60 seconds)
    end_time = time.time() + 60

    while time.time() < end_time:
        log_everything()
        # todo: download data
        # todo: take buy decision
        best_strategy()
        time.sleep(1)  # Pause for 1 second


def log_everything():
    # Create and configure logger
    logging.basicConfig(filename='example.log', format='%(asctime)s - %(message)s', level=logging.INFO)

    # Create a custom logger
    logger = logging.getLogger(__name__)

    # Create info log message
    logger.info('This is a info message from secondly_logger.py')


if __name__ == '__main__':
    log_secondly_for_one_minute()
