from abc import ABC, abstractmethod


class Device(ABC):
    device_name: ''

    def __init__(self):
        super().__init__()
        self.message_text: str = f'{self.device_name} reports:'

    @abstractmethod
    def process_data(self, input_data):
        pass


class Sender(Device):
    device_name = 'Sending device'

    def process_data(self, input_data):
        print(self.message_text, end=' ')
        print(f'process result: {input_data}!')
