import logging


def log_everything():
    # Create and configure logger
    logging.basicConfig(filename='/workspace/symmfony_docker/logs/example.log', format='%(asctime)s - %(message)s', level=logging.INFO)

    # Create a custom logger
    logger = logging.getLogger(__name__)

    # Create info log message
    logger.info('This is a info message from secondly_logger.py')


if __name__ == '__main__':
    log_everything()
