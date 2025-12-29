import 'package:isar/isar.dart';

part 'transaction_event.g.dart';

@Collection()
class TransactionEvent {
  Id id = Isar.autoIncrement;

  // Event attributes
  int? createdBy;
  int? confirmedBy;
  int? animalId;
  String? source;
  DateTime? eventDate;
  String? comment;
  bool? isConfirmed;
  String? uid;
  int? version;

  // Transaction specific
  String? transactionType;
  double? price;
  int? buyerId;
  int? sellerId;

  DateTime? createdAt;
  DateTime? updatedAt;
  DateTime? deletedAt;

  final creator = IsarLink<dynamic>();
  final confirmer = IsarLink<dynamic>();
  final animal = IsarLink<dynamic>();
  final buyer = IsarLink<dynamic>();
  final seller = IsarLink<dynamic>();

  TransactionEvent({
    this.createdBy,
    this.confirmedBy,
    this.animalId,
    this.source,
    this.eventDate,
    this.comment,
    this.isConfirmed,
    this.uid,
    this.version,
    this.transactionType,
    this.price,
    this.buyerId,
    this.sellerId,
    this.createdAt,
    this.updatedAt,
    this.deletedAt,
  });

  factory TransactionEvent.fromJson(Map<String, dynamic> json) {
    return TransactionEvent(
      createdBy: json['created_by'] as int?,
      confirmedBy: json['confirmed_by'] as int?,
      animalId: json['animal_id'] as int?,
      source: json['source'] as String?,
      eventDate: json['event_date'] == null
          ? null
          : DateTime.parse(json['event_date'] as String),
      comment: json['comment'] as String?,
      isConfirmed:
          json['is_confirmed'] == null ? null : (json['is_confirmed'] as bool),
      uid: json['uid'] as String?,
      version: json['version'] is int
          ? json['version'] as int
          : (json['version'] != null
              ? int.tryParse(json['version'].toString())
              : null),
      transactionType: json['transaction_type'] as String?,
      price: json['price'] == null ? null : (json['price'] as num).toDouble(),
      buyerId: json['buyer_id'] as int?,
      sellerId: json['seller_id'] as int?,
      createdAt: json['created_at'] == null
          ? null
          : DateTime.parse(json['created_at'] as String),
      updatedAt: json['updated_at'] == null
          ? null
          : DateTime.parse(json['updated_at'] as String),
      deletedAt: json['deleted_at'] == null
          ? null
          : DateTime.parse(json['deleted_at'] as String),
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'created_by': createdBy,
        'confirmed_by': confirmedBy,
        'animal_id': animalId,
        'source': source,
        'event_date': eventDate?.toIso8601String(),
        'comment': comment,
        'is_confirmed': isConfirmed,
        'uid': uid,
        'version': version,
        'transaction_type': transactionType,
        'price': price,
        'buyer_id': buyerId,
        'seller_id': sellerId,
        'created_at': createdAt?.toIso8601String(),
        'updated_at': updatedAt?.toIso8601String(),
        'deleted_at': deletedAt?.toIso8601String(),
      };
}
