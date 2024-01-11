import logging


def log_everything():
    # Create and configure logger
    logging.basicConfig(filename='../logs/example.log', format='%(asctime)s - %(message)s', level=logging.INFO)

    # Create a custom logger
    logger = logging.getLogger(__name__)

    # Create log messages
    logger.debug('This is a debug message')
    logger.info('This is an info message')
    logger.warning('This is a warning message')
    logger.error('This is an error message')
    logger.critical('This is a critical message')


if __name__ == '__main__':
    log_everything()
