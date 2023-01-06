<?php

namespace App\Http\Resources\Provider;

use App\Http\Resources\OAGFlightRecordResource;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Codeshare;
use App\Models\Enums\CodeType;
use App\Models\Enums\FlightType;
use App\Models\MasterFlight;
use App\Models\MasterFlightOverride;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class OAGResponseResource extends JsonResource
{
  public $override = null;
  public $flight_type = null;
  public $airline = null;
  public $airport = null;
  public $flight_number = null;

  /**
   * Create a new resource instance.
   *
   * @param  mixed  $resource
   * @return void
   */
  public function __construct($resource)
  {
      // Ensure you call the parent constructor
      parent::__construct($resource);
      $this->initMasterFlightOverrideRelatedAttributes();
  }

  private function initMasterFlightOverrideRelatedAttributes() {
    $this->override = new MasterFlightOverride(
      [ 'flight_guid' => $this->FlightGuid,
        // 'flight_type' => 'arrival',
        // 'flight_iata_code' => 'AA',
        // 'flight_code_type'=> 'iata'
      ]
    );
  }

  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
      return $this->resource;
  }

  private static function flightTypeCollection($resource, $flight_type) {
    $thisCollection = OAGResponseResource::collection($resource);
    return $thisCollection->map(function($i) use ($flight_type) {
      $i->flight_type = $flight_type;
      $i->validateFlightType();
      return $i;
    });
  }
  public static function arrivalFlightCollection($resource) { return OAGResponseResource::flightTypeCollection($resource, FlightType::ARRIVAL); }
  public static function departureFlightCollection($resource) { return OAGResponseResource::flightTypeCollection($resource, FlightType::DEPARTURE); }

  public function isArrival() {
    return strtoupper($this->flight_type) == FlightType::ARRIVAL;
  }

  public function isDeparture() {
    return strtoupper($this->flight_type) == FlightType::DEPARTURE;
  }

  private function validateFlightType() {
    if (!in_array($this->flight_type, [FlightType::ARRIVAL, FlightType::DEPARTURE]))
      throw new Exception('Invalid flight type set in resource.');
  }

  private function assignCodeProps(&$masterFlightObj, $masterFlightObjCodeKey, $oagCodeSchemaObj) {
    $typeKey = $masterFlightObjCodeKey . '_type';

    if (!isset($oagCodeSchemaObj)) return;
    $masterFlightObj->{$masterFlightObjCodeKey} = isset($oagCodeSchemaObj->Code) ? $oagCodeSchemaObj->Code : null;
    if (!isset($oagCodeSchemaObj->CodeNamespace)) return;
    switch (strtoupper($oagCodeSchemaObj->CodeNamespace)) {
      case CodeType::IATA:
        $masterFlightObj->{$typeKey} = CodeType::IATA;
        break;
      case CodeType::ICAO:
        $masterFlightObj->{$typeKey} = CodeType::IATA;
        break;
      case CodeType::FAA:
        $masterFlightObj->{$typeKey} = CodeType::IATA;
        break;
      default:
        $masterFlightObj->{$typeKey} = isset($oagCodeSchemaObj->CodeNamespace) ? $oagCodeSchemaObj->CodeNamespace : null;
    }
  }

  private function assignStatusProps(&$masterFlightObj, $masterFlightStatusKey, $oagStatusSchemaObj) {
    if (isset($oagStatusSchemaObj)) {
      $timeKey = $masterFlightStatusKey . '_time_utc';
      $accuracyKey = $masterFlightStatusKey . '_accuracy';
      $sourceKey = $masterFlightStatusKey . '_source';
      $masterFlightObj->{$timeKey} = $this->makeMySQLDateTimeFriendly($oagStatusSchemaObj->DateTimeLocal);
      $masterFlightObj->{$accuracyKey} = $oagStatusSchemaObj->Accuracy;
      $masterFlightObj->{$sourceKey} = $oagStatusSchemaObj->SourceType;
    }
  }

  private function updateDelay($masterFlightId, $delay, $delayType) {
    return null;
    // TODO: All this jazz
  }

  private function updateDepartureDelays($masterFlightId, $delays) {
    foreach($delays as $delay) {
      $this->updateDelay($masterFlightId, $delay, FlightType::DEPARTURE);
    }
  }

  private function updateArrivalDelays($masterFlightId, $delays) {
    foreach($delays as $delay) {
      $this->updateDelay($masterFlightId, $delay, FlightType::ARRIVAL);
    }
  }

  private function assignAircraftData(&$masterFlight, $aircraftSchemaObj) {
    if (!isset($aircraftSchemaObj)) return;
    $this->assignCodeProps($masterFlight, 'aircraft_code', $aircraftSchemaObj);
    if (isset($aircraftSchemaObj->WeightClass)) $masterFlight->aircraft_weight_class = $aircraftSchemaObj->WeightClass;
    if (isset($aircraftSchemaObj->TailNumber)) $masterFlight->aircraft_tail_number = $aircraftSchemaObj->TailNumber;
  }

  private function assignFlightPositionData(&$masterFlight, $mapData, $positioningSchemaData) {
    $masterFlight->aircraft_map_position = $mapData;
    if (isset($positioningSchemaData)) {
      $masterFlight->aircraft_altitude = $positioningSchemaData->Altitude;
      $masterFlight->aircraft_speed = $positioningSchemaData->Speed;
      $masterFlight->aircraft_heading = $positioningSchemaData->Heading;
    }
    // Not handling route data atm
  }

  private function makeMySQLDateTimeFriendly($dt) {
    return date('Y-m-d H:i:s', strtotime($dt));
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function toMasterFlight() : MasterFlight
  {
    $masterFlight = new MasterFlight();
    $masterFlight->u_id = $this->FlightGuid;
    $masterFlight->flight_type = $this->flight_type;
    $masterFlight->flight_number = $this->Acid->FlightNumber;
    if (isset($this->Acid->Airline))
      $this->assignCodeProps($masterFlight, 'airline_code', $this->Acid->Airline);
    $this->assignCodeProps($masterFlight, 'departure_airport_code', $this->DepartureAirport);
    $masterFlight->scheduled_departure_utc = $this->makeMySQLDateTimeFriendly($this->ScheduledDeparture->Local);
    $this->assignCodeProps($masterFlight, 'arrival_airport_code', $this->ArrivalAirport);
    $masterFlight->scheduled_arrival_utc =  $this->makeMySQLDateTimeFriendly($this->ScheduledArrival->Local);
    $this->assignCodeProps($masterFlight, 'airport_faa_code', $this->FaaId);
    $masterFlight->scheduled_duration = $this->ScheduledDuration;
    $masterFlight->remaining_time = $this->RemainingTime;
    $masterFlight->elapsed_time = $this->ElapsedTime;
    if (isset($this->RegionalCarrier)) $this->assignCodeProps($masterFlight, 'regional_carrier_code', $this->RegionalCarrier);
    $this->assignStatusProps($masterFlight, 'in_air', $this->InAir);
    $this->assignStatusProps($masterFlight, 'in_gate', $this->InGate);
    $this->assignStatusProps($masterFlight, 'out_gate', $this->OutGate);
    $this->assignStatusProps($masterFlight, 'landed', $this->Landed);
    $masterFlight->departure_terminal = $this->DepartureTerminal;
    $masterFlight->departure_gate = $this->DepartureGate;
    $masterFlight->arrival_terminal = $this->ArrivalTerminal;
    $masterFlight->arrival_gate = $this->ArrivalGate;
    $masterFlight->check_in_counter = $this->CheckinCounter;
    $masterFlight->baggage = $this->Baggage;
    $masterFlight->service_type = $this->ServiceType;
    $this->assignFlightPositionData($masterFlight, $this->Map, $this->Position);
    $this->assignAircraftData($masterFlight, $this->Aircraft);
    if (isset($this->AircraftPreviousFlightLeg)) {
      if (isset($this->AircraftPreviousFlightLeg->FlightId) && isset($this->AircraftPreviousFlightLeg->FlightId->Airline)) {
        $this->assignCodeProps($masterFlight, 'previous_flight_airline_code', $this->AircraftPreviousFlightLeg->FlightId->Airline);
        $masterFlight->previous_flight_airline_flight_number = $this->AircraftPreviousFlightLeg->FlightId->FlightNumber;
          }
      $masterFlight->previous_flight_scheduled_arrival_utc = $this->makeMySQLDateTimeFriendly($this->AircraftPreviousFlightLeg->ScheduledArrival->Local);
        }
    $masterFlight->scheduled = $this->Scheduled;
    $masterFlight->general_aviation = $this->GeneralAviation;
    $masterFlight->status = $this->Status;
    $masterFlight->departure_schedule_status = $this->DepartureScheduleStatus;
    $masterFlight->arrival_schedule_status = $this->ArrivalScheduleStatus;
    $masterFlight->leg = $this->LegSequenceNumber;
    $masterFlight->total_legs = $this->NumLegs;
    $masterFlight->origination_date = $this->makeMySQLDateTimeFriendly($this->OriginationDate->Local);
    $masterFlight->diversion_status = $this->DiversionStatus;
    $masterFlight->codeshares = (new Codeshare)->newCollection(array_map(function($codeshare) {
        $airline = isset($codeshare->Airline) ? $codeshare->Airline : null;
        if (!is_null($airline)) {
          $airline_code = isset($airline->Code) ? $airline->Code : null;
          $airline_code_type = isset($airline->CodeNamespace) ? $airline->CodeNamespace : null;
          $airline_name = isset($airline->Name) ? $airline->Name : null;
        }
        return new Codeshare([
          'airline_code' =>  $airline_code,
          'airline_code_type' => $airline_code_type,
          'flight_number' => isset($codeshare->FlightNumber) ? $codeshare->FlightNumber : null,
          'airline_name' => $airline_name,
        ]);
      }, isset($this->Codeshares) ? $this->Codeshares : [])
    );
    return $masterFlight;
  }
}
