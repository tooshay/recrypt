# Recrypt

A simple API to store and encrypt, and fetch and decrypt values.


### Installation

* Run `git clone https://github.com/tooshay/recrypt.git`
* Edit `.env` file to set correct database environment variables
* Run `composer update` to fetch external dependencies
* Run `php artisan key:generate`
* Run `php artisan migrate`
* Run `php artisan serve` or choose your preferred means for running this locally

### Running tests
I've opted for [PEST](https://pestphp.com/), _the_ new testing framework going around. To run the tests, run `./vendor/bin/pest`

### Questions
1. Q: What would you add to your solution if you had more time? A: I'd probably move out the encrypting logic to a service to clean up my controllers and test that in isolation. I'd write a more comprehensive set of tests.
2. Q: How did you encrypt the data and why? A: I chose Laravel's encryption facade. It seemed like the obvious choice as it's part of the framework, relies on teh widely accepted and used OpenSSL library and a message authentication code to prevent tampering with encrypted values. 
3. Q: How would you test this solution for performance in production? A: I can't see this particular solution presenting any obvious performance issues but were this to become more complicated/process-heavy, I'd look to an external tool (JMeter?) to load test the API and see where the bottlenecks are. Otherwise I'd look at logs, DB query times etc. 
4. Please describe yourself using JSON:
```json
{
    "name": "Roy Shay",
    "gender": "Male",
    "address": [
        {
            "street": "2 Buckingham Ave.",
            "city": "West Molesey",
            "county": "Surrey",
            "postcode": "KT8 1TG"
        }
    ],
    "favourite_foods": [
        {
            "chinese": [
                {
                    "favourite_dishes": [
                        "Crispy Duck",
                        "Gong Bao Chicken"
                    ]
                }
            ],
            "middle eastern": [
                {
                    "favourite_dishes": [
                        "Hummus",
                        "Siniya"
                    ]
                }
            ]
        }
    ]
}
```
