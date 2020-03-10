**Graduate Academy – End of Phase Two Task**
**The problem**

In order to add some new and exciting products to our systems, we need to process a CSV file from a supplier.

This file contains product information which we need to extract and insert into a database table.

In addition, we need to apply some simple business rules to the data we import.

**The Solution**

You need to write an application which will read the CSV file, parse the contents and then insert the data into a MySQL database.

The import process will begin from a new user interface (web based).

You’ll need to report how many items were processed, how many were successful, and how many were skipped. See the import rules below.

**Objectives**

Your solution must use PHP in an OO style and MySQL. Code should be clearly laid out and well commented.

An SQL file should be provided to create the initial database tables required by the code.

To show you understand some of the areas you’ve been studying recently, the following should be considered:
* You’ll need to create an application using the Symfony framework
* A user interface should be included, consisting of a (Symfony) form for
the user to upload a CSV from
* Validation rules (to cover the import rules below) should be covered
* A user interface should be included, to summarise the progress of
product imports and , if relevant, any reasons for failure
* A user interface should be included, to summarise all existing
products in the database
* Some unit tests should be included
* RabbitMQ should be used to process the upload (i.e. the CSV should be parsed, validated and a message published for each record for processing via a Rabbit consumer)

**Import Rules**

The csv columns should contain:
* Product Code
* Product Name
* Product Description
* Product Manufacture
* Stock
* Net Cost 
* Tax Rate
* Discontinued

Any stock item which costs less that £5 and has less than 10 stock will not be imported. Any stock items which cost over £1000 will not be imported.

Any stock item marked as discontinued will be imported, but will have the discontinued date set as the current date.

Any items which fail to be inserted correctly need to be listed in a report at the end of the import process.

**Additional Considerations**

Because the data is from an external source, it may present certain issues. These include:
1. Whether the data is correctly formatted for CSV  
1. Whether the data is correctly formatted for use with a database  
1. Potential data encoding issues or line termination problems  
1. Manual interference with the file which may invalidate some entries  
