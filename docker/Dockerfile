FROM tutum/lamp:latest
MAINTAINER George Dawoud <george@dawouds.com>

# Download latest version of ChurchCRM into /app
RUN rm -fr /app && git clone https://github.com/ChurchCRM/CRM.git /churchcrm && mv /churchcrm/src /app
