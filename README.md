# cinemaApp

This is the application that simulate cinema management, consists of APIs to support seat ticket/schedule management. Main users are the schedule planner, the booking counter (cashier) and the remote client (mobile app, website), please note that this application still has not implemented user authentication part, money accounting part.

## Tools and environment
PHP         : version 7.0.6

Database    : MongoDB v3.2.6

PHP MongoDB driver  : version 1.1.6 (for PHP7)

Framework
* Laravel 5.2.33
* Jenseggers Laravel MongoDB (for some libraries to connect Laravel with MongoDB)

## Models
Since we come to the cinema for watching movies, let me starts from the movie.

__Movie__ has it's own attribute that makes people remember, title, genre, release date. For this application I chose attributes that I think needed for the schedule management which are __movie title__ and __duration__.
Movie database will always be generated/modified by the schedule planner as new movie comes.

Next, the __Theater__, Theater is needed to be a model because we needs its attributes to manage seats/tickets, the theater name (in this application I only call it 'number'), number of rows, number of seats per row. Theater information will be used when a new schedule created, __seat information__ will be supplied so that booking counter/customer can see which seats are available for booking/buy for the specified schedule (show time)

We already have 2 main resources, now we want to manage them with time, so we have the __Schedule__. (Actually I'm not really sure if I use a proper word) The schedule obviously has __show time__ as one of the attributes, as well as __movie title__, __date__, and also theater information such as __theater name__ (number), __available seats__, and __reserved seats__ (booked). I take the available seats (in fact the whole Theater document) under Schedule because each schedule has their own list of available seats, when we search for a schedule we can know the seat information right away. You might also see I mentioned reserved seats here, this is used when booking happens, people can books seats before they claim the ticket (as well as pay the money), there is also a case that people change their mind and leave the booking number, in that case the reserved list will eventually be merged back to be available seats (this normally automated by the system, I only provide an API for this task, if needed)

I reuse the Schedule model (Schedule collection in the database) to store booking and purchasing information, the document will have schedule id (primary key: _id) to make it easy to find back to the schedule for other information. Purchasing info document has more information stored than the booking info document, since the purchased seat has been pulled out of the available list, later we dont need to bother with the schedule document anymore.

### Model Summarize
__Movie__
  * name (movie title)
  * duration (movie duration/length)

__Theater__
  * num (theater name)
  * seats (2-dimentional array of seat rows(in character) and seat numbers(in integer))

__Schedule__
  * name (movie title)
  * date (show time date)
  * time (show time)
  * Theater (num, availableseats (2-dimentional array), reservedseats(2-dimentional array, if there is reserved seats for the particular schedule)

__Schedule__ (booking info)
  * scheduleid  (primary key of the booked schedule)
  * seats   (2-dimentional array just like Theater's seats)
  * bookid  (booking ID, 6-bytes random generated)

__Schedule__ (purchase info)
  * scheduleid   (primary key of the booked schedule)
  * name  (movie title)
  * date  (show time date)
  * time  (show time)
  * thearter (theater name or number)
  * seats (2-dimentional array just like Theater's seats)
  * purchaseid (6-bytes random generated, purchase ID to claim the ticket)

## Controller
__MovieController__ responsible for CRUD operation of the Movie model to database.
            
__TheaterController__ responsible for CRUD operation of the Theater model to database.

__ScheduleController__ responsible for CRUD operation of the Schedule model to database (this part user will be the schedule planner), shedule queries, seat reservation (book/buy). There will always cross-check with both Movie and Theater if infomation are valid during the operation. (example: we cant create schedule that movie name is not in the database, when create schedule that theater name not in the database.) 



## View
I only made some pages for testing the APIs, not much here.
planning.php (/planning), to test APIs to MovieController, TheaterController, ScheduleController on Create/Update/Delete part.
ticketing.php (/counter), to test APIs on booking/buying seats, cancel booking, etc.


## APIs overview

APIs created in this application are simulated from how we do business on cinema.
3 main users here,
* Schedule planner will use APIs to create schedule, starts from create movies, create theater, then create schedule. Later some movies are out-date he might want to delete them from the database. Normally planner should have access to know everything, but probably not booking information.
* Booking counter or cashier, normally has access to APIs that do schedule querying, checking booking information from the customer, buy ticket/seat for the customer (no booking). 
* Customer, they could do some movie schedule searching (querying) then book the seat, or just buy the seat with online payment method. The transactions will eventually generate booking ID for the customer.

## API endpoints

Most of the API will return result in JSON, except for some delete method will return 'SUCCESS'.
On error, returns status (HTTP status) and some message.

1. /planning/movie

   GET /planning/movie 
   POST /planning/movie/create                                   To create new movie
   
2. /planning/theater
3. /planning/schedule
