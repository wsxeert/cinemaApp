# cinemaApp

This is the application that simulate cinema management, consists of APIs to support seat ticket/schedule management. Main users are the schedule planner, the booking counter (cashier) and the remote client (mobile app, website), please note that this application still has not implemented user authentication part, money accounting part.

## Tools and environment
PHP         : version 7.0.6
Database    : MongoDB v3.2.6
PHP MongoDB driver  : version 1.1.6 (for PHP7)
Framework   : Laravel 5.2.33
            : Jenseggers Laravel MongoDB (for some libraries to connect Laravel with MongoDB)

## Models
Since we come to the cinema for watching movies, let me starts from the movie.

__Movie__ has it's own attribute that makes people remember, title, genre, release date. For this application I chose attributes that I think needed for the schedule management which are __movie title__ and __duration__.
Movie database will always be generated/modified by the schedule planner as new movie comes.

Next, the __Theater__, Theater is needed to be a model because we needs its attributes to manage seats/tickets, the theater name (in this application I only call it 'number'), number of rows, number of seats per row. Theater information will be used when a new schedule created, __seat information__ will be supplied so that booking counter/customer can see which seats are available for booking/buy for the specified schedule (show time)

We already have 2 main resources, now we want to manage them with time, so we have the __Schedule__. (Actually I'm not really sure if I use a proper word) The schedule obviously has __show time__ as one of the attributes, as well as __movie title__, __date__, and also theater information such as __theater name__ (number), __available seats__, and __reserved seats__ (booked). I take the available seats (in fact the whole Theater document) under Schedule because each schedule has their own list of available seats, when we search for a schedule we can know the seat information right away. You might also see I mentioned reserved seats here, this is used when booking happens, people can books seats before they claim the ticket (as well as pay the money), there is also a case that people change their mind and leave the booking number, in that case the reserved list will eventually be merged back to be available seats (this normally automated by the system, I only provide an API for this task, if needed)

I reuse the Schedule model (Schedule collection in the database) to store booking and purchasing information, the document will have schedule id (primary key: _id) to make it easy to find back to the schedule for other information. Purchasing information has more information stored, since the purchased seat has been pulled out of the available list, later we dont need to bother with the schedule document anymore.

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
  * bookid  (booking ID)

__Schedule__ (purchase info)
  * scheduleid   (primary key of the booked schedule)
  * name  (movie title)
  * date  (show time date)
  * time  (show time)
  * thearter (theater name or number)
  * seats (2-dimentional array just like Theater's seats)
